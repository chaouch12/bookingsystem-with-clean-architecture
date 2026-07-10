<?php

declare(strict_types=1);

namespace App\Layers\Infrastructure\Persistence;

use App\Layers\Domain\Shared\Event\DomainEvent;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineDomainEventsExtractor
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * @return list<DomainEvent>
     */
    public function extract(): array
    {
        $unitOfWork = $this->entityManager->getUnitOfWork();
        $entities = array_merge(
            $unitOfWork->getScheduledEntityInsertions(),
            $unitOfWork->getScheduledEntityUpdates(),
            $unitOfWork->getScheduledEntityDeletions(),
        );

        $events = [];

        foreach ($entities as $entity) {
            if (!method_exists($entity, 'releaseDomainEvents')) {
                continue;
            }

            /** @var list<DomainEvent> $entityEvents */
            $entityEvents = $entity->releaseDomainEvents();
            array_push($events, ...$entityEvents);
        }

        return $events;
    }
}
