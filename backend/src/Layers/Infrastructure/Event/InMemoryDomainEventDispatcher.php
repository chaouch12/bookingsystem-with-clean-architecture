<?php

declare(strict_types=1);

namespace App\Layers\Infrastructure\Event;

use App\Layers\Application\Shared\Event\DomainEventDispatcher;
use App\Layers\Application\Shared\Event\DomainEventHandler;
use App\Layers\Domain\Shared\Event\DomainEvent;

final class InMemoryDomainEventDispatcher implements DomainEventDispatcher
{
    /**
     * @var array<class-string<DomainEvent>, list<DomainEventHandler<DomainEvent>>>
     */
    private array $listeners;

    /**
     * @param iterable<DomainEventHandler<DomainEvent>> $handlers
     */
    public function __construct(iterable $handlers = [])
    {
        $this->listeners = [];

        foreach ($handlers as $handler) {
            $this->listeners[$handler::listensTo()][] = $handler;
        }
    }

    public function dispatch(DomainEvent ...$events): void
    {
        foreach ($events as $event) {
            foreach ($this->listeners[$event::class] ?? [] as $listener) {
                $listener->handle($event);
            }
        }
    }
}
