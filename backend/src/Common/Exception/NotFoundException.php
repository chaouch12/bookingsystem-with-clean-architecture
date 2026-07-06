<?php

declare(strict_types=1);

namespace App\Common\Exception;

use RuntimeException;

class NotFoundException extends RuntimeException
{
    public static function forResource(string $resourceName, int|string|null $identifier = null): self
    {
        if ($identifier === null) {
            return new self(sprintf('%s not found.', $resourceName));
        }

        return new self(sprintf('%s not found for identifier "%s".', $resourceName, (string) $identifier));
    }
}
