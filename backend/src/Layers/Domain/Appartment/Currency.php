<?php

declare(strict_types=1);

namespace App\Layers\Domain\Appartment;

use InvalidArgumentException;

final class Currency
{
    /** @var Currency[] */
    public static array $ALL;

    private function __construct(
        public readonly string $code,
    ) {}

    public static function fromCode(string $code): self
    {
        foreach (self::$ALL as $currency) {
            if ($currency->code === $code) {
                return $currency;
            }
        }

        throw new InvalidArgumentException('The currency code is invalid.');
    }
}