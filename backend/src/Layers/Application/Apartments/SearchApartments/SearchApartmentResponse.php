<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\SearchApartments;

final readonly class SearchApartmentResponse
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public string $priceAmount,
        public string $priceCurrency,
        public string $cleaningFeeAmount,
        public string $cleaningFeeCurrency,
        public string $street,
        public string $streetNumber,
        public string $zipcode,
        public string $city,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            (int) $row['id'],
            (string) $row['name'],
            isset($row['description']) ? (string) $row['description'] : null,
            (string) $row['price_amount'],
            (string) $row['price_currency'],
            (string) $row['cleaning_fee_amount'],
            (string) $row['cleaning_fee_currency'],
            (string) $row['address_street'],
            (string) $row['address_street_number'],
            (string) $row['address_zipcode'],
            (string) $row['address_city'],
        );
    }
}
