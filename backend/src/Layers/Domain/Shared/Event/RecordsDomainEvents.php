<?php

declare(strict_types=1);

namespace App\Layers\Domain\Shared\Event;

trait RecordsDomainEvents
{
    /** @var list<DomainEvent> */
    private array $domainEvents = [];

    protected function recordDomainEvent(DomainEvent $event): void
    {
        $this->domainEvents[] = $event;
    }

    /**
     * @return list<DomainEvent>
     */
    public function releaseDomainEvents(): array
    {
        $releasedEvents = $this->domainEvents;
        $this->domainEvents = [];

        return $releasedEvents;
    }
}
