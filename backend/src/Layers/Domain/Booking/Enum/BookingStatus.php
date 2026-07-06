<?php

declare(strict_types=1);

namespace App\Layers\Domain\Booking\Enum;

enum BookingStatus: string
{
    case RESERVED = 'reserved';
    case CONFIRMED = 'confirmed';
    case REJECTED = 'rejected';
    case CANCELLED = 'cancelled';
}
