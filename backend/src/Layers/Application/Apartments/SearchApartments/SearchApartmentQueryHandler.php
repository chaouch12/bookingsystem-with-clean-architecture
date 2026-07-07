<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\SearchApartments;

use App\Layers\Application\Shared\Messaging\QueryHandler;
use App\Layers\Domain\Booking\Enum\BookingStatus;
use App\Layers\Domain\Shared\ResultWithValue;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;

/**
 * @implements QueryHandler<SearchApartmentQuery, ResultWithValue<list<SearchApartmentResponse>>>
 */
final readonly class SearchApartmentQueryHandler implements QueryHandler
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @return ResultWithValue<list<SearchApartmentResponse>>
     */
    public function handle(object $query): ResultWithValue
    {
        if ($query->startDate > $query->endDate) {
            return ResultWithValue::successWithValue($this->emptyResponseList());
        }

        /** @var list<array<string, mixed>> $rows */
        $rows = $this->connection->fetchAllAssociative(
            <<<'SQL'
                SELECT
                    a.id,
                    a.name,
                    a.description,
                    a.price_amount,
                    a.price_currency,
                    a.cleaning_fee_amount,
                    a.cleaning_fee_currency,
                    a.address_street,
                    a.address_street_number,
                    a.address_zipcode,
                    a.address_city
                FROM appartment AS a
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM booking AS b
                    WHERE
                        b.appartment_id = a.id
                        AND b.check_in < :endDate
                        AND b.check_out > :startDate
                        AND b.status IN (:activeBookingStatuses)
                )
                ORDER BY a.name ASC
            SQL,
            [
                'startDate' => $query->startDate->format('Y-m-d'),
                'endDate' => $query->endDate->format('Y-m-d'),
                'activeBookingStatuses' => [
                    BookingStatus::RESERVED->value,
                    BookingStatus::CONFIRMED->value,
                ],
            ],
            [
                'activeBookingStatuses' => ArrayParameterType::STRING,
            ],
        );

        return ResultWithValue::successWithValue(
            array_map(
                static fn (array $row): SearchApartmentResponse => SearchApartmentResponse::fromRow($row),
                $rows,
            ),
        );
    }

    /**
     * @return list<SearchApartmentResponse>
     */
    private function emptyResponseList(): array
    {
        return [];
    }
}
