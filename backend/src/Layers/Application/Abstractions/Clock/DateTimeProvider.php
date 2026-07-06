<?php

declare(strict_types=1);

namespace App\Layers\Application\Abstractions\Clock;

use DateTimeImmutable;

interface DateTimeProvider
{
    public function utcNow(): DateTimeImmutable;
}
