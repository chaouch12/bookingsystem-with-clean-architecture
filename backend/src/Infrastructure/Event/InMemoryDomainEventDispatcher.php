<?php

declare(strict_types=1);

namespace App\Infrastructure\Event;

use App\Application\Shared\Event\DomainEventDispatcher;
use App\Layers\Domain\Shared\Event\DomainEvent;

final class InMemoryDomainEventDispatcher implements DomainEventDispatcher
{
    /**
     * @var array<class-string<DomainEvent>, list<callable(DomainEvent): void>>
     */
    private array $listeners;

    /**
     * @param array<class-string<DomainEvent>, list<callable(DomainEvent): void>> $listeners
     */
    public function __construct(array $listeners = [])
    {
        $this->listeners = [];

        foreach ($listeners as $eventClass => $eventListeners) {
            $this->listeners[$eventClass] = $eventListeners;
        }
    }

    public function dispatch(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            foreach ($this->listeners[$event::class] ?? [] as $listener) {
                $listener($event);
            }
        }
    }
}
