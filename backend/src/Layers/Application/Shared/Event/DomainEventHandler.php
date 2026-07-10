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
     * @return class-string<TEvent>
     */
    public static function listensTo(): string;

    /**
     * @param TEvent $event
     */
    public function handle(DomainEvent $event): void;
}
