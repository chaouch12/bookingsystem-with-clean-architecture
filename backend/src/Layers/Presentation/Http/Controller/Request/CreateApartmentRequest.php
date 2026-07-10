<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Http\Controller\Request;

use App\Layers\Domain\Appartment\Enum\Currency;
use DateTimeInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Constraints as Assert;

#[OA\Schema(schema: 'CreateApartmentRequest')]
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
    #[OA\Property(enum: ['', 'USD', 'EUR'])]
    #[Assert\Choice(callback: [Currency::class, 'getValues'])]
    public string $priceCurrency;

    #[Assert\NotBlank]
    public string $cleaningFeeAmount;

    #[Assert\NotBlank]
    #[OA\Property(enum: ['', 'USD', 'EUR'])]
    #[Assert\Choice(callback: [Currency::class, 'getValues'])]
    public string $cleaningFeeCurrency;

    #[Assert\DateTime(format: DateTimeInterface::ATOM)]
    public ?string $lastBookedOnUtc = null;

    /** @var list<int> */
    #[Assert\Type('array')]
    #[OA\Property(type: 'array', items: new OA\Items(type: 'integer', enum: [1, 2, 3, 4, 5, 6, 7, 8, 9]))]
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
