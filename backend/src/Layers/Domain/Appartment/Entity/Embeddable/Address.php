<?php

declare(strict_types=1);

namespace App\Layers\Domain\Appartment\Entity\Embeddable;

use App\Util\Normalization\StreetNormalizer;
use App\Util\Normalization\StreetNumberNormalizer;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final readonly class Address
{
    #[ORM\Column(nullable: false)]
    public string $street;

    #[ORM\Column(length: 20, nullable: false)]
    public string $streetNumber;

    #[ORM\Column(length: 20, nullable: false)]
    public string $zipcode;

    #[ORM\Column(nullable: false)]
    public string $city;

    public function __construct(
        string $street,
        string $streetNumber,
        string $zipcode,
        string $city,
    ) {
        // If any field is provided, all must be provided and non-empty
        if (trim($street) === '') {
            throw new InvalidArgumentException('Street is required when creating an Address');
        }
        if (trim($streetNumber) === '') {
            throw new InvalidArgumentException('Street number is required when creating an Address');
        }
        if (trim($zipcode) === '') {
            throw new InvalidArgumentException('Zipcode is required when creating an Address');
        }
        if (trim($city) === '') {
            throw new InvalidArgumentException('City is required when creating an Address');
        }

        $this->street = StreetNormalizer::normalize($street);
        $this->streetNumber = StreetNumberNormalizer::normalize($streetNumber);
        $this->zipcode = trim($zipcode);
        $this->city = trim($city);

    }

    public function getFullAddress(): string
    {
        $streetPart = $this->street;
        $streetPart .= ' ' . $this->streetNumber;
        $parts = array_filter([$streetPart, $this->zipcode, $this->city]);

        return implode(', ', $parts);
    }

    public function __toString(): string
    {
        return $this->getFullAddress();
    }
}
