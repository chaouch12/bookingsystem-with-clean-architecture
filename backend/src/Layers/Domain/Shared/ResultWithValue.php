<?php

declare(strict_types=1);

namespace App\Layers\Domain\Shared;

use RuntimeException;

/**
 * @template TValue
 */
final class ResultWithValue extends Result
{
    /**
     * @param TValue|null $value
     */
    private function __construct(
        private readonly mixed $value,
        bool $isSuccess,
        Error $error,
    ) {
        parent::__construct($isSuccess, $error);
    }

    /**
     * @return TValue
     */
    public function value(): mixed
    {
        if ($this->isFailure()) {
            throw new RuntimeException('The value of a failure result cannot be accessed.');
        }

        return $this->value;
    }

    /**
     * @template TCreate
     *
     * @param TCreate $value
     *
     * @return self<TCreate>
     */
    public static function successWithValue(mixed $value): self
    {
        return new self($value, true, Error::none());
    }

    /**
     * @return self<mixed>
     */
    public static function failureWithError(Error $error): self
    {
        return new self(null, false, $error);
    }

    /**
     * @return self<mixed>
     */
    public static function create(mixed $value): self
    {
        if ($value === null) {
            return self::failureWithError(Error::nullValue());
        }

        return self::successWithValue($value);
    }
}
