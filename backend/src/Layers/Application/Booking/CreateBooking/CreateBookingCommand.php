<?php

declare(strict_types=1);

namespace App\Layers\Application\Booking\CreateBooking;

use App\Layers\Application\Shared\Messaging\Command;
use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use App\Layers\Domain\Booking\ValueObject\GuestCount;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateBookingCommand implements Command
{
    public function __construct(
        #[Assert\Positive]
        public int $appartmentId,
        #[Assert\Positive]
        public int $guestUserId,
        #[Assert\NotNull]
        public BookingPeriod $period,
        #[Assert\NotNull]
        public GuestCount $guestCount,
    ) {
    }
}
