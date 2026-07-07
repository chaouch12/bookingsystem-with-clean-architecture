<?php

declare(strict_types=1);

namespace App\Layers\Application\Booking\GetBooking;

use App\Layers\Application\Shared\Messaging\Query;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetBookingQuery implements Query
{
    public function __construct(
        #[Assert\Positive]
        public int $bookingId,
    ) {
    }
}
