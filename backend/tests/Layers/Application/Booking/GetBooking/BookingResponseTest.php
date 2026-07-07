<?php

declare(strict_types=1);

namespace App\Tests\Layers\Application\Booking\GetBooking;

use App\Layers\Application\Booking\GetBooking\BookingResponse;
use PHPUnit\Framework\TestCase;

final class BookingResponseTest extends TestCase
{
    public function testItBuildsFlatResponseFromDatabaseRow(): void
    {
        $response = BookingResponse::fromRow([
            'id' => 5,
            'appartment_id' => 10,
            'guest_user_id' => 20,
            'status' => 'reserved',
            'price_for_period_amount' => '480.00',
            'price_for_period_currency' => 'EUR',
            'cleaning_fee_amount' => '35.00',
            'cleaning_fee_currency' => 'EUR',
            'amenities_up_charge_amount' => '24.00',
            'amenities_up_charge_currency' => 'EUR',
            'total_price_amount' => '539.00',
            'total_price_currency' => 'EUR',
            'check_in' => '2026-08-01',
            'check_out' => '2026-08-05',
            'guest_count' => 2,
            'created_at' => '2026-07-06 10:00:00',
            'confirmed_on_utc' => null,
            'rejected_on_utc' => null,
            'cancelled_on_utc' => null,
        ]);

        self::assertSame(5, $response->id);
        self::assertSame(10, $response->appartmentId);
        self::assertSame(20, $response->guestUserId);
        self::assertSame('reserved', $response->status);
        self::assertSame('480.00', $response->priceForPeriodAmount);
        self::assertSame('EUR', $response->priceForPeriodCurrency);
        self::assertSame('2026-08-01', $response->checkIn);
        self::assertSame('2026-08-05', $response->checkOut);
    }
}
