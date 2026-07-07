<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\SearchApartments;

use App\Layers\Domain\Booking\Enum\BookingStatus;
use DateTimeImmutable;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

readonly class SearchApartmentReadRepository
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @return list<SearchApartmentResponse>
     */
    public function searchAvailable(DateTimeImmutable $startDate, DateTimeImmutable $endDate): array
    {
        /** @var list<array<string, mixed>> $rows */
        $rows = $this->connection->createQueryBuilder()
            ->select(
                'a.id',
                'a.name',
                'a.description',
                'a.price_amount',
                'a.price_currency',
                'a.cleaning_fee_amount',
                'a.cleaning_fee_currency',
                'a.address_street',
                'a.address_street_number',
                'a.address_zipcode',
                'a.address_city',
            )
            ->from('appartment', 'a')
            ->where(
                'NOT EXISTS (
                    SELECT 1
                    FROM booking b
                    WHERE b.appartment_id = a.id
                      AND b.check_in < :endDate
                      AND b.check_out > :startDate
                      AND b.status IN (:activeBookingStatuses)
                )',
            )
            ->orderBy('a.name', 'ASC')
            ->setParameter('startDate', $startDate->format('Y-m-d'))
            ->setParameter('endDate', $endDate->format('Y-m-d'))
            ->setParameter(
                'activeBookingStatuses',
                [
                    BookingStatus::RESERVED->value,
                    BookingStatus::CONFIRMED->value,
                ],
                ArrayParameterType::STRING,
            )
            ->fetchAllAssociative();

        return array_map(
            static fn (array $row): SearchApartmentResponse => SearchApartmentResponse::fromRow($row),
            $rows,
        );
    }
}
