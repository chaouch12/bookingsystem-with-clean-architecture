<?php

declare(strict_types=1);

namespace App\Tests\Layers\Application\Apartments\SearchApartments;

use App\Layers\Application\Apartments\SearchApartments\SearchApartmentResponse;
use PHPUnit\Framework\TestCase;

final class SearchApartmentResponseTest extends TestCase
{
    public function testItBuildsFlatApartmentResponseFromRow(): void
    {
        $response = SearchApartmentResponse::fromRow([
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
        ]);

        self::assertSame(10, $response->id);
        self::assertSame('Mountain Loft', $response->name);
        self::assertSame('100.00', $response->priceAmount);
        self::assertSame('EUR', $response->priceCurrency);
        self::assertSame('Main Street', $response->street);
        self::assertSame('Berlin', $response->city);
    }
}
