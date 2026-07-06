<?php

declare(strict_types=1);

namespace App\Layers\Domain\Shared;

use InvalidArgumentException;

class Result
{
    protected function __construct(
        public readonly bool $isSuccess,
        public readonly Error $error,
    ) {
        if ($this->isSuccess && !$this->error->isNone()) {
            throw new InvalidArgumentException('A successful result cannot contain an error.');
        }

        if (!$this->isSuccess && $this->error->isNone()) {
            throw new InvalidArgumentException('A failed result must contain an error.');
        }
    }

    public function isFailure(): bool
    {
        return !$this->isSuccess;
    }

    public static function success(): self
    {
        return new self(true, Error::none());
    }

    public static function failure(Error $error): self
    {
        return new self(false, $error);
    }
}
