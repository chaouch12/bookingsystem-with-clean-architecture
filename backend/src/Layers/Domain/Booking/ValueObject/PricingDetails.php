<?php

declare(strict_types=1);

namespace App\Layers\Domain\Booking\ValueObject;

use App\Layers\Domain\Appartment\Money;

final readonly class PricingDetails
{
    public function __construct(
        public Money $priceForPeriod,
        public Money $cleaningFee,
        public Money $amenitiesUpCharge,
        public Money $totalPrice,
    ) {
    }
}
