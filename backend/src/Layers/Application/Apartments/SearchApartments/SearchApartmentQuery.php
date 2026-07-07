<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\SearchApartments;

use App\Layers\Application\Shared\Messaging\Query;
use DateTimeImmutable;

final readonly class SearchApartmentQuery implements Query
{
    public function __construct(
        public DateTimeImmutable $startDate,
        public DateTimeImmutable $endDate,
    ) {
    }
}
