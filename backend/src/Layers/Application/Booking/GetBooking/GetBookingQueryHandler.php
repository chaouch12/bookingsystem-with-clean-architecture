<?php

declare(strict_types=1);

namespace App\Layers\Application\Booking\GetBooking;

use App\Layers\Application\Shared\Messaging\QueryHandler;
use App\Layers\Domain\Booking\BookingErrors;
use App\Layers\Domain\Shared\ResultWithValue;
use Doctrine\DBAL\Connection;

/**
 * @implements QueryHandler<GetBookingQuery, ResultWithValue<BookingResponse>>
 */
final readonly class GetBookingQueryHandler implements QueryHandler
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @return ResultWithValue<BookingResponse>
     */
    public function handle(object $query): ResultWithValue
    {
        /** @var array<string, mixed>|false $row */
        $row = $this->connection->fetchAssociative(
            <<<'SQL'
                SELECT
                    id,
                    appartment_id,
                    guest_user_id,
                    status,
                    price_for_period_amount,
                    price_for_period_currency,
                    cleaning_fee_amount,
                    cleaning_fee_currency,
                    amenities_up_charge_amount,
                    amenities_up_charge_currency,
                    total_price_amount,
                    total_price_currency,
                    check_in,
                    check_out,
                    guest_count,
                    created_at,
                    confirmed_on_utc,
                    rejected_on_utc,
                    cancelled_on_utc
                FROM booking
                WHERE id = :bookingId
            SQL,
            ['bookingId' => $query->bookingId],
        );

        if ($row === false) {
            return ResultWithValue::failureWithError(BookingErrors::notFound());
        }

        return ResultWithValue::successWithValue(BookingResponse::fromRow($row));
    }
}
