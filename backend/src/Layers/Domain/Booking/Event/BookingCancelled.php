<?php

declare(strict_types=1);

namespace App\Layers\Domain\Booking\Event;

use App\Layers\Domain\Shared\Event\DomainEvent;
use DateTimeImmutable;

final readonly class BookingCancelled implements DomainEvent
{
    public function __construct(
        public int $bookingId,
        private DateTimeImmutable $occurredOn = new DateTimeImmutable(),
    ) {
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public static function eventName(): string
    {
        return 'booking.cancelled';
    }
}
