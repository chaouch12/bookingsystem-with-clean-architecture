<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Http\Controller\Response;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ApartmentResponse')]
final readonly class ApartmentResponse
{
    /**
     * @param list<AmenityResponse> $amenities
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $description,
        public MoneyResponse $price,
        public MoneyResponse $cleaningFee,
        public ?string $lastBookedOnUtc,
        #[OA\Property(type: 'array', items: new OA\Items(ref: new Model(type: AmenityResponse::class)))]
        public array $amenities,
        public AddressResponse $address,
    ) {
    }
}
