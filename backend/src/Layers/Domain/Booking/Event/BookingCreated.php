<?php

declare(strict_types=1);

namespace App\Layers\Domain\Booking\Event;

use App\Layers\Domain\Booking\Enum\BookingStatus;
use App\Layers\Domain\Shared\Event\DomainEvent;
use DateTimeImmutable;

final readonly class BookingCreated implements DomainEvent
{
    public function __construct(
        public int $appartmentId,
        public int $guestUserId,
        public DateTimeImmutable $checkIn,
        public DateTimeImmutable $checkOut,
        public BookingStatus $status,
        private DateTimeImmutable $occurredOn = new DateTimeImmutable(),
    ) {
    }

    public function occurredOn(): DateTimeImmutable
    {
        return $this->occurredOn;
    }

    public static function eventName(): string
    {
        return 'booking.created';
    }
}
