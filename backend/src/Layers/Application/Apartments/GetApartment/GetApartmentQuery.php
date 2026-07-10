<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\GetApartment;

use App\Layers\Application\Shared\Messaging\Query;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetApartmentQuery implements Query
{
    public function __construct(
        #[Assert\Positive]
        public int $id,
    ) {
    }
}
