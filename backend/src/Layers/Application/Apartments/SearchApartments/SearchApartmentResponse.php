<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\SearchApartments;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class SearchApartmentResponse
{
    public function __construct(
        #[Assert\Positive]
        public int $id,
        #[Assert\NotBlank]
        public string $name,
        public ?string $description,
        #[Assert\NotBlank]
        public string $priceAmount,
        #[Assert\NotBlank]
        public string $priceCurrency,
        #[Assert\NotBlank]
        public string $cleaningFeeAmount,
        #[Assert\NotBlank]
        public string $cleaningFeeCurrency,
        #[Assert\NotBlank]
        public string $street,
        #[Assert\NotBlank]
        public string $streetNumber,
        #[Assert\NotBlank]
        public string $zipcode,
        #[Assert\NotBlank]
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
