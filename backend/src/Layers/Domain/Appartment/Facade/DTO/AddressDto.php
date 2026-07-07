<?php

declare(strict_types=1);

namespace App\Layers\Domain\Appartment\Facade\DTO;

use Symfony\Component\Validator\Constraints as Assert;

readonly class AddressDto
{
    public function __construct(
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
