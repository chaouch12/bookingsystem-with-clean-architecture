<?php

declare(strict_types=1);

namespace App\src\Layers\Application\Booking\CreateBooking;

use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use App\Layers\Domain\Booking\ValueObject\GuestCount;

final readonly class CreateBookingCommand
{
    public function __construct(
        public int $appartmentId,
        public int $guestUserId,
        public BookingPeriod $period,
        public GuestCount $guestCount,
    ) {
    }
}
