<?php

declare(strict_types=1);

namespace App\Layers\Infrastructure\Persistence;

use App\Layers\Application\Shared\Persistence\TransactionManager;
use App\Layers\Infrastructure\Event\DomainEventsPublisher;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineTransactionManager implements TransactionManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private DoctrineDomainEventsExtractor $domainEventsExtractor,
        private DomainEventsPublisher $domainEventsPublisher,
    ) {
    }

    public function flushAndPublish(): void
    {
        $events = $this->domainEventsExtractor->extract();
        $this->entityManager->flush();
        $this->domainEventsPublisher->publish($events);
    }
}
