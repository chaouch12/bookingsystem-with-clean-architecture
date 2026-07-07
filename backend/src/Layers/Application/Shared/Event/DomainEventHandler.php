<?php

declare(strict_types=1);

namespace App\Layers\Application\Shared\Event;

use App\Layers\Domain\Shared\Event\DomainEvent;

/**
 * @template TEvent of DomainEvent
 */
interface DomainEventHandler
{
    /**
     * @param TEvent $event
     */
    public function handle(DomainEvent $event): void;
}
