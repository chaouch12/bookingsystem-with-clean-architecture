<?php

declare(strict_types=1);

namespace App\Layers\Presentation\Crm\Apartment\Models\Payload;

use OpenApi\Attributes as OA;

#[OA\Schema(schema: 'UpdateApartmentPayload')]
final class UpdateApartmentPayload extends CreateApartmentPayload
{
}
