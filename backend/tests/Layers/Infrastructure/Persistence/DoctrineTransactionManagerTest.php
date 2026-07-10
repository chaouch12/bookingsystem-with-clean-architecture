<?php

declare(strict_types=1);

namespace App\Tests\Layers\Infrastructure\Persistence;

use App\Layers\Application\Shared\Event\DomainEventDispatcher;
use App\Layers\Domain\Booking\Enum\BookingStatus;
use App\Layers\Domain\Booking\Event\BookingCreated;
use App\Layers\Infrastructure\Event\DomainEventsPublisher;
use App\Layers\Infrastructure\Persistence\DoctrineDomainEventsExtractor;
use App\Layers\Infrastructure\Persistence\DoctrineTransactionManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;

final class DoctrineTransactionManagerTest extends TestCase
{
    public function testItFlushesThenPublishesExtractedEvents(): void
    {
        $flushed = false;
        $events = [
            new BookingCreated(
                10,
                20,
                new \DateTimeImmutable('2026-08-01'),
                new \DateTimeImmutable('2026-08-05'),
                BookingStatus::RESERVED,
            ),
        ];

        $unitOfWork = $this->createMock(UnitOfWork::class);
        $unitOfWork->expects(self::once())
            ->method('getScheduledEntityInsertions')
            ->willReturn([
                new class($events) {
                    public function __construct(private array $events)
                    {
                    }

                    public function releaseDomainEvents(): array
                    {
                        return $this->events;
                    }
                },
            ]);
        $unitOfWork->expects(self::once())->method('getScheduledEntityUpdates')->willReturn([]);
        $unitOfWork->expects(self::once())->method('getScheduledEntityDeletions')->willReturn([]);

        $entityManager = $this->createMock(EntityManagerInterface::class);
        $entityManager->expects(self::once())
            ->method('getUnitOfWork')
            ->willReturn($unitOfWork);
        $entityManager->expects(self::once())
            ->method('flush')
            ->willReturnCallback(function () use (&$flushed): void {
                $flushed = true;
            });

        $dispatcher = $this->createMock(DomainEventDispatcher::class);
        $dispatcher->expects(self::once())
            ->method('dispatch')
            ->with(...$events)
            ->willReturnCallback(function () use (&$flushed): void {
                self::assertTrue($flushed);
            });

        $publisher = new DomainEventsPublisher($dispatcher);
        $extractor = new DoctrineDomainEventsExtractor($entityManager);

        $transactionManager = new DoctrineTransactionManager(
            $entityManager,
            $extractor,
            $publisher,
        );

        $transactionManager->flushAndPublish();
    }
}
