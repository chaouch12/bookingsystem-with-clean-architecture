<?php

declare(strict_types=1);

namespace App\Application\Shared\Event;

use App\Layers\Domain\Shared\Event\DomainEvent;

interface DomainEventDispatcher
{
    public function dispatch(DomainEvent ...$events): void;
}
