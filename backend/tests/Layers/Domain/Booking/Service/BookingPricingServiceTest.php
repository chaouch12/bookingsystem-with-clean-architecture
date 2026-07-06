<?php

declare(strict_types=1);

namespace App\Tests\Layers\Domain\Booking\Service;

use App\Layers\Domain\Appartment\Entity\Appartment;
use App\Layers\Domain\Appartment\Entity\Embeddable\Address;
use App\Layers\Domain\Appartment\Enum\Amenity;
use App\Layers\Domain\Appartment\Enum\Currency;
use App\Layers\Domain\Appartment\Money;
use App\Layers\Domain\Booking\Service\BookingPricingService;
use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use PHPUnit\Framework\TestCase;

final class BookingPricingServiceTest extends TestCase
{
    public function testItCalculatesPriceWithAmenityUpchargesAndCleaningFee(): void
    {
        $appartment = new Appartment(
            'Mountain Loft',
            null,
            new Money('100.00', Currency::EUR),
            new Money('20.00', Currency::EUR),
            null,
            [Amenity::MountainView, Amenity::AirConditioning, Amenity::Parking],
            new Address('Main Street', '1', '12345', 'Berlin'),
        );
        $period = new BookingPeriod(
            new \DateTimeImmutable('2026-08-01'),
            new \DateTimeImmutable('2026-08-04'),
        );

        $pricingDetails = (new BookingPricingService())->calculatePrice($appartment, $period);

        self::assertSame('300.00', $pricingDetails->priceForPeriod->amount);
        self::assertSame('20.00', $pricingDetails->cleaningFee->amount);
        self::assertSame('21.00', $pricingDetails->amenitiesUpCharge->amount);
        self::assertSame('341.00', $pricingDetails->totalPrice->amount);
    }

    public function testItCalculatesPriceWithoutCleaningFeeOrAmenityUpcharges(): void
    {
        $appartment = new Appartment(
            'Plain Flat',
            null,
            new Money('80.00', Currency::EUR),
            new Money('0.00', Currency::EUR),
            null,
            [Amenity::WIFI],
            new Address('Side Street', '5', '54321', 'Hamburg'),
        );
        $period = new BookingPeriod(
            new \DateTimeImmutable('2026-09-10'),
            new \DateTimeImmutable('2026-09-12'),
        );

        $pricingDetails = (new BookingPricingService())->calculatePrice($appartment, $period);

        self::assertSame('160.00', $pricingDetails->priceForPeriod->amount);
        self::assertSame('0.00', $pricingDetails->cleaningFee->amount);
        self::assertSame('0.00', $pricingDetails->amenitiesUpCharge->amount);
        self::assertSame('160.00', $pricingDetails->totalPrice->amount);
    }
}
