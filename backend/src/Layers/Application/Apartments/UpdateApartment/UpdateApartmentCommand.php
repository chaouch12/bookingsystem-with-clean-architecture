<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\UpdateApartment;

use App\Layers\Application\Shared\Messaging\Command;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class UpdateApartmentCommand implements Command
{
    /**
     * @param list<int> $amenities
     */
    public function __construct(
        #[Assert\Positive]
        public int $id,
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 64)]
        public string $name,
        #[Assert\Length(max: 1000)]
        public ?string $description,
        #[Assert\NotBlank]
        public string $priceAmount,
        #[Assert\NotBlank]
        public string $priceCurrency,
        #[Assert\NotBlank]
        public string $cleaningFeeAmount,
        #[Assert\NotBlank]
        public string $cleaningFeeCurrency,
        #[Assert\DateTime(format: DateTimeInterface::ATOM)]
        public ?string $lastBookedOnUtc,
        #[Assert\Type('array')]
        public array $amenities,
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
}
