<?php

declare(strict_types=1);

namespace App\Layers\Domain\Booking\Service;

use App\Layers\Domain\Appartment\Entity\Appartment;
use App\Layers\Domain\Appartment\Enum\Amenity;
use App\Layers\Domain\Appartment\Money;
use App\Layers\Domain\Booking\ValueObject\BookingPeriod;
use App\Layers\Domain\Booking\ValueObject\PricingDetails;

final readonly class BookingPricingService
{
    public function calculatePrice(Appartment $appartment, BookingPeriod $period): PricingDetails
    {
        $currency = $appartment->getPrice()->currency;

        $priceForPeriod = new Money(
            number_format((float) $appartment->getPrice()->amount * $period->nights(), 2, '.', ''),
            $currency,
        );

        $percentageUpCharge = 0.0;
        foreach ($appartment->getAmenities() as $amenity) {
            $percentageUpCharge += match ($amenity) {
                Amenity::MountainView => 0.05,
                Amenity::AirConditioning, Amenity::Parking => 0.01,
                default => 0.0,
            };
        }

        $amenitiesUpCharge = new Money('0.00', $currency);
        if ($percentageUpCharge > 0) {
            $amenitiesUpCharge = new Money(
                number_format((float) $priceForPeriod->amount * $percentageUpCharge, 2, '.', ''),
                $currency,
            );
        }

        $totalPrice = $priceForPeriod;
        if ((float) $appartment->getCleaningFee()->amount > 0.0) {
            $totalPrice = $totalPrice->add($appartment->getCleaningFee());
        }
        $totalPrice = $totalPrice->add($amenitiesUpCharge);

        return new PricingDetails(
            $priceForPeriod,
            $appartment->getCleaningFee(),
            $amenitiesUpCharge,
            $totalPrice,
        );
    }
}
