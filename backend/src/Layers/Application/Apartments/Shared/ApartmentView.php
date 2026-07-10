<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\Shared;

/**
 * @param list<int> $amenities
 */
final readonly class ApartmentView
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public string $priceAmount,
        public string $priceCurrency,
        public string $cleaningFeeAmount,
        public string $cleaningFeeCurrency,
        public ?string $lastBookedOnUtc,
        public array $amenities,
        public string $street,
        public string $streetNumber,
        public string $zipcode,
        public string $city,
    ) {
    }
}
