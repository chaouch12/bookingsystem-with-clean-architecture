<?php

declare(strict_types=1);

namespace App\Layers\Domain\Shared\Event;

use DateTimeImmutable;

interface DomainEvent
{
    public function occurredOn(): DateTimeImmutable;

    public static function eventName(): string;
}
