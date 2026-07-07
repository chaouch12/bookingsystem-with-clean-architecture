<?php

declare(strict_types=1);

namespace App\Tests\Layers\Application\Apartments\SearchApartments;

use App\Layers\Application\Apartments\SearchApartments\SearchApartmentQuery;
use App\Layers\Application\Apartments\SearchApartments\SearchApartmentQueryHandler;
use App\Layers\Application\Apartments\SearchApartments\SearchApartmentResponse;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;

final class SearchApartmentQueryHandlerTest extends TestCase
{
    public function testItReturnsEmptyListWhenDateRangeIsInvalid(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects(self::never())->method('fetchAllAssociative');

        $result = (new SearchApartmentQueryHandler($connection))->handle(
            new SearchApartmentQuery(
                new \DateTimeImmutable('2026-08-05'),
                new \DateTimeImmutable('2026-08-01'),
            ),
        );

        self::assertTrue($result->isSuccess);
        self::assertSame([], $result->value());
    }

    public function testItReturnsAvailableApartmentsAsFlatResponses(): void
    {
        $connection = $this->createMock(Connection::class);
        $connection
            ->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(
                self::stringContains('FROM appartment AS a'),
                [
                    'startDate' => '2026-08-01',
                    'endDate' => '2026-08-05',
                    'activeBookingStatuses' => ['reserved', 'confirmed'],
                ],
                [
                    'activeBookingStatuses' => ArrayParameterType::STRING,
                ],
            )
            ->willReturn([
                [
                    'id' => 10,
                    'name' => 'Mountain Loft',
                    'description' => 'Nice stay',
                    'price_amount' => '100.00',
                    'price_currency' => 'EUR',
                    'cleaning_fee_amount' => '20.00',
                    'cleaning_fee_currency' => 'EUR',
                    'address_street' => 'Main Street',
                    'address_street_number' => '1',
                    'address_zipcode' => '12345',
                    'address_city' => 'Berlin',
                ],
            ]);

        $result = (new SearchApartmentQueryHandler($connection))->handle(
            new SearchApartmentQuery(
                new \DateTimeImmutable('2026-08-01'),
                new \DateTimeImmutable('2026-08-05'),
            ),
        );

        self::assertTrue($result->isSuccess);
        self::assertCount(1, $result->value());
        self::assertInstanceOf(SearchApartmentResponse::class, $result->value()[0]);
        self::assertSame('Mountain Loft', $result->value()[0]->name);
    }
}
