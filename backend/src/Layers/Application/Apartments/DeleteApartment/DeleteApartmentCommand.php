<?php

declare(strict_types=1);

namespace App\Layers\Application\Apartments\DeleteApartment;

use App\Layers\Application\Shared\Messaging\Command;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class DeleteApartmentCommand implements Command
{
    public function __construct(
        #[Assert\Positive]
        public int $id,
    ) {
    }
}
