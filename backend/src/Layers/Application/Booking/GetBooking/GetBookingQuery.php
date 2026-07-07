<?php

declare(strict_types=1);

namespace App\Layers\Application\Booking\GetBooking;

use App\Layers\Application\Shared\Messaging\Query;

final readonly class GetBookingQuery implements Query
{
    public function __construct(
        public int $bookingId,
    ) {
    }
}
