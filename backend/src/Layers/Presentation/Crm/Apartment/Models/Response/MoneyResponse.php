<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Crm\Apartment\Models\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'MoneyResponse')]
final readonly class MoneyResponse
{
    public function __construct(
        public string $amount,
        public string $currency,
    ) {
    }
}
