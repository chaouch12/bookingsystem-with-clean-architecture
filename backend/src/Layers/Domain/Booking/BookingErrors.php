<?php

declare(strict_types=1);

namespace App\Layers\Domain\Booking;

use App\Layers\Domain\Shared\Error;

final class BookingErrors
{
    public static function notPending(): Error
    {
        return new Error('Booking.NotPending', 'Booking is not in reserved status.');
    }

    public static function notConfirmed(): Error
    {
        return new Error('Booking.NotConfirmed', 'Booking is not confirmed.');
    }

    public static function alreadyStarted(): Error
    {
        return new Error('Booking.AlreadyStarted', 'Booking has already started.');
    }
}
