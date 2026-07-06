<?php

declare(strict_types=1);

namespace App\Layers\Domain\Users;

use App\Layers\Domain\Shared\Error;

final class UserErrors
{
    public static function notFound(): Error
    {
        return new Error('User.NotFound', 'User was not found.');
    }
}
