<?php

declare(strict_types=1);

namespace App\Layers\Domain\Appartment\Facade\DTO;

readonly class AddressDto
{
    public function __construct(
        public string $street,
        public string $streetNumber,
        public string $zipcode,
        public string $city,
    ) {
    }

    public function getFullAddress(): string
    {
        $streetPart = $this->street.' '.$this->streetNumber;

        return implode(', ', [$streetPart, $this->zipcode, $this->city]);
    }

    public function __toString(): string
    {
        return $this->getFullAddress();
    }
}
