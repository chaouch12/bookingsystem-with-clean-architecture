<?php

declare(strict_types=1);

namespace App\Layers\Application\Booking\GetBooking;

use Doctrine\DBAL\Connection;

readonly class GetBookingReadRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    public function findById(int $bookingId): ?BookingResponse
    {
        /** @var array<string, mixed>|false $row */
        $row = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'appartment_id',
                'guest_user_id',
                'status',
                'price_for_period_amount',
                'price_for_period_currency',
                'cleaning_fee_amount',
                'cleaning_fee_currency',
                'amenities_up_charge_amount',
                'amenities_up_charge_currency',
                'total_price_amount',
                'total_price_currency',
                'check_in',
                'check_out',
                'guest_count',
                'created_at',
                'confirmed_on_utc',
                'rejected_on_utc',
                'cancelled_on_utc',
            )
            ->from('booking')
            ->where('id = :bookingId')
            ->setParameter('bookingId', $bookingId)
            ->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return BookingResponse::fromRow($row);
    }
}
