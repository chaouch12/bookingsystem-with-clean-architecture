<?php

declare(strict_types=1);

namespace App\Tests\Layers\Infrastructure\Event;

use App\Layers\Application\Shared\Event\DomainEventHandler;
use App\Layers\Domain\Booking\Enum\BookingStatus;
use App\Layers\Domain\Booking\Event\BookingCreated;
use App\Layers\Domain\Users\Enum\UserRoleType;
use App\Layers\Domain\Users\Event\UserRegistered;
use App\Layers\Infrastructure\Event\InMemoryDomainEventDispatcher;
use PHPUnit\Framework\TestCase;

final class InMemoryDomainEventDispatcherTest extends TestCase
{
    public function testItDispatchesEventsOnlyToMatchingHandlers(): void
    {
        $bookingEvents = new \ArrayObject();
        $userEvents = new \ArrayObject();

        $dispatcher = new InMemoryDomainEventDispatcher([
            new class($bookingEvents) implements DomainEventHandler {
                public function __construct(private \ArrayObject $collector)
                {
                }

                public static function listensTo(): string
                {
                    return BookingCreated::class;
                }

                public function handle(\App\Layers\Domain\Shared\Event\DomainEvent $event): void
                {
                    $this->collector->append($event);
                }
            },
            new class($userEvents) implements DomainEventHandler {
                public function __construct(private \ArrayObject $collector)
                {
                }

                public static function listensTo(): string
                {
                    return UserRegistered::class;
                }

                public function handle(\App\Layers\Domain\Shared\Event\DomainEvent $event): void
                {
                    $this->collector->append($event);
                }
            },
        ]);

        $dispatcher->dispatch(
            new BookingCreated(
                10,
                20,
                new \DateTimeImmutable('2026-08-01'),
                new \DateTimeImmutable('2026-08-05'),
                BookingStatus::RESERVED,
            ),
            new UserRegistered('guest@example.com', 'Guest User', UserRoleType::MANAGER),
        );

        self::assertCount(1, $bookingEvents);
        self::assertCount(1, $userEvents);
        self::assertInstanceOf(BookingCreated::class, $bookingEvents[0]);
        self::assertInstanceOf(UserRegistered::class, $userEvents[0]);
    }
}
