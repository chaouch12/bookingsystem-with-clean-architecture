<?php

declare(strict_types=1);

namespace App\Tests\Layers\Application\Booking\GetBooking;

use App\Layers\Application\Booking\GetBooking\BookingResponse;
use App\Layers\Application\Booking\GetBooking\GetBookingQuery;
use App\Layers\Application\Booking\GetBooking\GetBookingQueryHandler;
use App\Layers\Domain\Booking\BookingErrors;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class GetBookingQueryHandlerTest extends TestCase
{
    public function testItReturnsFlatBookingResponseWhenRecordExists(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('fetchAssociative')
            ->with(
                self::stringContains('FROM booking'),
                ['bookingId' => 5],
            )
            ->willReturn([
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

        $result = (new GetBookingQueryHandler($connection))->handle(new GetBookingQuery(5));

        self::assertTrue($result->isSuccess);
        self::assertInstanceOf(BookingResponse::class, $result->value());
        self::assertSame(5, $result->value()->id);
        self::assertSame(10, $result->value()->appartmentId);
        self::assertSame('reserved', $result->value()->status);
    }

    public function testItReturnsFailureWhenBookingDoesNotExist(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('fetchAssociative')
            ->willReturn(false);

        $result = (new GetBookingQueryHandler($connection))->handle(new GetBookingQuery(999));

        self::assertTrue($result->isFailure());
        self::assertSame(BookingErrors::notFound()->code, $result->error->code);
    }
}
