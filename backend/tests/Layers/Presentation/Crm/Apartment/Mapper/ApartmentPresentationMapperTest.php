<?php

declare(strict_types=1);

namespace App\Tests\Layers\Presentation\Crm\Apartment\Mapper;

use App\Layers\Application\Apartments\Shared\ApartmentView;
use App\Layers\Presentation\Crm\Apartment\Mapper\ApartmentPresentationMapper;
use PHPUnit\Framework\TestCase;

final class ApartmentPresentationMapperTest extends TestCase
{
    public function testItMapsApplicationViewToPresentationResponse(): void
    {
        $mapper = new ApartmentPresentationMapper();

        $response = $mapper->toResponse(new ApartmentView(
            10,
            'Mountain Loft',
            'Nice stay',
            '100.00',
            'EUR',
            '20.00',
            'EUR',
            '2026-08-01T10:00:00+00:00',
            [1, 3],
            'Main Street',
            '1',
            '12345',
            'Berlin',
        ));

        self::assertSame(10, $response->id);
        self::assertSame('Mountain Loft', $response->name);
        self::assertSame('EUR', $response->price->currency);
        self::assertCount(2, $response->amenities);
        self::assertSame('WIFI', $response->amenities[0]->name);
        self::assertSame('Main Street 1, 12345, Berlin', $response->address->full);
    }
}
