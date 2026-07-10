<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Internal\Health\Models\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'HealthResponse')]
final readonly class HealthResponse
{
    public function __construct(
        public string $status,
        public string $service,
        public string $php,
    ) {
    }
}
