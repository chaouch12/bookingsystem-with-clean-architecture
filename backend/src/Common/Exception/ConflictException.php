<?php

declare(strict_types=1);

namespace App\Common\Exception;

use RuntimeException;

class ConflictException extends RuntimeException
{
    public static function because(string $message): self
    {
        return new self($message);
    }
}
