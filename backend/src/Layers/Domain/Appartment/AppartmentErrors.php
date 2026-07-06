<?php

declare(strict_types=1);

namespace App\Layers\Domain\Appartment;

use App\Layers\Domain\Shared\Error;

final class AppartmentErrors
{
    public static function notFound(): Error
    {
        return new Error('Appartment.NotFound', 'Appartment was not found.');
    }
}
