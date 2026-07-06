<?php

declare(strict_types=1);

namespace App\Layers\Domain\Users\Enum;

enum UserStatusType: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case AGENT = 'agent';
    case READONLY = 'readonly';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
