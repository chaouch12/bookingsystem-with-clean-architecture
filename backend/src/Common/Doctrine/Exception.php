<?php

declare(strict_types=1);

namespace App\Common\Doctrine;

use RuntimeException;

class Exception
{
    public static function NonPersistedEntityException(): RuntimeException
    {
        return new RuntimeException('Non-Persisted entity');
    }
}
