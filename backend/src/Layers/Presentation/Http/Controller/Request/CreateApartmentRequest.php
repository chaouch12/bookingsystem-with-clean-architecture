<?php

declare(strict_types=1);

namespace App\src\Layers\Presentation\Http\Controller\Request;

use App\Layers\Domain\Appartment\Enum\Currency;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateApartmentRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 64)]
    public string $name;

    #[Assert\Length(max: 1000)]
    public ?string $description = null;

    #[Assert\NotBlank]
    public string $priceAmount;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [Currency::class, 'getValues'])]
    public string $priceCurrency;

    #[Assert\NotBlank]
    public string $cleaningFeeAmount;

    #[Assert\NotBlank]
    #[Assert\Choice(callback: [Currency::class, 'getValues'])]
    public string $cleaningFeeCurrency;

    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public ?string $lastBookedOnUtc = null;

    /** @var list<int> */
    #[Assert\Type('array')]
    public array $amenities = [];

    #[Assert\NotBlank]
    public string $street;

    #[Assert\NotBlank]
    public string $streetNumber;

    #[Assert\NotBlank]
    public string $zipcode;

    #[Assert\NotBlank]
    public string $city;
}
