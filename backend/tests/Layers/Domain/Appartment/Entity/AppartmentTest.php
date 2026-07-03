<?php

declare(strict_types=1);

namespace App\Tests\Layers\Domain\Appartment\Entity;

use App\Layers\Domain\Appartment\Entity\Appartment;
use App\Layers\Domain\Appartment\Entity\Embeddable\Address;
use App\Layers\Domain\Appartment\Enum\Amenity;
use App\Layers\Domain\Appartment\Enum\Currency;
use App\Layers\Domain\Appartment\Money;
use PHPUnit\Framework\TestCase;

final class AppartmentTest extends TestCase
{
    public function testGetAmenitiesNormalizesHydratedScalarValues(): void
    {
        $appartment = new Appartment(
            'Test apartment',
            'Description',
            new Money('10000', Currency::EUR),
            new Money('2500', Currency::EUR),
            null,
            [Amenity::WIFI],
            new Address('Main Street', '1', '12345', 'Berlin'),
        );

        $reflection = new \ReflectionProperty($appartment, 'amenities');
        $reflection->setValue($appartment, [1, 3]);

        self::assertSame(
            [Amenity::WIFI, Amenity::Parking],
            $appartment->getAmenities(),
        );
    }
}
