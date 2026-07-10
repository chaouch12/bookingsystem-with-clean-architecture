<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Http\Controller\Response;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'ApiMessageResponse')]
final readonly class ApiMessageResponse
{
    public function __construct(
        public string $message,
    ) {
    }
}
