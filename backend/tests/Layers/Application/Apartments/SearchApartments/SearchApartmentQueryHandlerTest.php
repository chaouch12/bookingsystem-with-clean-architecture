<?php

declare(strict_types=1);

namespace App\Tests\Layers\Application\Apartments\SearchApartments;

use App\Layers\Application\Apartments\SearchApartments\SearchApartmentQuery;
use App\Layers\Application\Apartments\SearchApartments\SearchApartmentQueryHandler;
use App\Layers\Application\Apartments\SearchApartments\SearchApartmentReadRepository;
use App\Layers\Application\Apartments\SearchApartments\SearchApartmentResponse;
use PHPUnit\Framework\TestCase;

final class SearchApartmentQueryHandlerTest extends TestCase
{
    public function testItReturnsEmptyListWhenDateRangeIsInvalid(): void
    {
        $readRepository = $this->createMock(SearchApartmentReadRepository::class);
        $readRepository->expects(self::never())->method('searchAvailable');

        $result = (new SearchApartmentQueryHandler($readRepository))->handle(
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
        $readRepository = $this->createMock(SearchApartmentReadRepository::class);
        $readRepository
            ->expects(self::once())
            ->method('searchAvailable')
            ->with(
                new \DateTimeImmutable('2026-08-01'),
                new \DateTimeImmutable('2026-08-05'),
            )
            ->willReturn([
                new SearchApartmentResponse(
                    10,
                    'Mountain Loft',
                    'Nice stay',
                    '100.00',
                    'EUR',
                    '20.00',
                    'EUR',
                    'Main Street',
                    '1',
                    '12345',
                    'Berlin',
                ),
            ]);

        $result = (new SearchApartmentQueryHandler($readRepository))->handle(
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
