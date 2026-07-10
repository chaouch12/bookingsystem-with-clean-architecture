<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Http\Controller\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'AddressResponse')]
final readonly class AddressResponse
{
    public function __construct(
        public string $street,
        public string $streetNumber,
        public string $zipcode,
        public string $city,
        public string $full,
    ) {
    }
}
