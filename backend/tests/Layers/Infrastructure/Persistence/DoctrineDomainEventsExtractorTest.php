<?php

declare(strict_types=1);

namespace App\Tests\Layers\Infrastructure\Persistence;

use App\Layers\Domain\Booking\Entity\Booking;
use App\Layers\Domain\Booking\Event\BookingCreated;
use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use App\Layers\Domain\Booking\ValueObject\GuestCount;
use App\Layers\Domain\Appartment\Money;
use App\Layers\Domain\Appartment\Enum\Currency;
use App\Layers\Infrastructure\Persistence\DoctrineDomainEventsExtractor;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use PHPUnit\Framework\TestCase;

final class DoctrineDomainEventsExtractorTest extends TestCase
{
    public function testItExtractsAndClearsDomainEventsFromScheduledEntities(): void
    {
        $booking = Booking::create(
            10,
            20,
            new BookingPeriod(
                new \DateTimeImmutable('2026-08-01'),
                new \DateTimeImmutable('2026-08-05'),
            ),
            new GuestCount(2),
            new Money('480.00', Currency::EUR),
            new Money('35.00', Currency::EUR),
            new Money('24.00', Currency::EUR),
            new Money('539.00', Currency::EUR),
        );

        $unitOfWork = $this->createStub(UnitOfWork::class);
        $unitOfWork->method('getScheduledEntityInsertions')->willReturn([$booking]);
        $unitOfWork->method('getScheduledEntityUpdates')->willReturn([]);
        $unitOfWork->method('getScheduledEntityDeletions')->willReturn([]);

        $entityManager = $this->createStub(EntityManagerInterface::class);
        $entityManager->method('getUnitOfWork')->willReturn($unitOfWork);

        $extractor = new DoctrineDomainEventsExtractor($entityManager);

        $events = $extractor->extract();

        self::assertCount(1, $events);
        self::assertInstanceOf(BookingCreated::class, $events[0]);
        self::assertSame([], $booking->releaseDomainEvents());
    }
}
