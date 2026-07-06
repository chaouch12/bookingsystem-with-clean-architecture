<?php

declare(strict_types=1);

namespace App\Layers\Domain\Shared;

final readonly class Error
{
    public function __construct(
        public string $code,
        public string $name,
    ) {
    }

    public static function none(): self
    {
        return new self('', '');
    }

    public static function nullValue(): self
    {
        return new self('Error.NullValue', 'Null value was provided');
    }

    public function isNone(): bool
    {
        return $this->code === '' && $this->name === '';
    }
}
