<?php

declare(strict_types=1);

namespace App\Layers\Infrastructure\Event;

use App\Layers\Application\Shared\Event\DomainEventDispatcher;
use App\Layers\Domain\Shared\Event\DomainEvent;

final readonly class DomainEventsPublisher
{
    public function __construct(
        private DomainEventDispatcher $dispatcher,
    ) {
    }

    /**
     * @param list<DomainEvent> $events
     */
    public function publish(array $events): void
    {
        if ($events === []) {
            return;
        }

        $this->dispatcher->dispatch(...$events);
    }
}
