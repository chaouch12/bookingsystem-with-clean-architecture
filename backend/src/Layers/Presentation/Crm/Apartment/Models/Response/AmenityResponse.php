<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Crm\Apartment\Models\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'AmenityResponse')]
final readonly class AmenityResponse
{
    public function __construct(
        public string $name,
        public int $value,
    ) {
    }
}
