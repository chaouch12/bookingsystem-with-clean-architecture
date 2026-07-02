<?php

declare(strict_types=1);

namespace App\Layers\Domain\Appartment\Enum;

enum Currency: string
{
    case NONE = '';

    case USD = 'USD';

    case EUR = 'EUR';

    /**
     * Get all values.
     *
     * @return string[]
     */
    public static function getValues(): array
    {
        return array_column(self::cases(), 'value');
    }

}
