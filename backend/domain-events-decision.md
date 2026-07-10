# Domain Events Decision

## Version

- Current version: `2.0`
- Last updated: `2026-07-10`

## Change Log

### 2.0

- moved from manual handler-level dispatch to centralized `flushAndPublish()` transaction handling
- added Doctrine-based domain event extraction from scheduled entities
- added central event publication through `DomainEventDispatcher`
- added explicit domain event handler registration via service tags

### 1.0

- kept manual handler-level dispatch as an explicit temporary approach

## Decision

Domain events are now published through a centralized persistence flow.

Current pattern:

1. aggregate records domain events internally
2. application handler saves the aggregate without flushing events manually
3. `TransactionManager::flushAndPublish()` extracts domain events from scheduled Doctrine entities
4. Doctrine flush is executed
5. extracted events are published through `DomainEventDispatcher`

Example in the current codebase:

- `Booking` records events through `RecordsDomainEvents`
- `CreateBookingCommandHandler` saves the booking and then calls `flushAndPublish()`

## Why we changed it

We moved to the centralized approach shown in the screenshot because the save-then-dispatch behavior had become a real architectural concern and the repo already had the right primitives in place.

Reasons:

### 1. Publication responsibility belonged at the persistence boundary

The save-then-dispatch flow should not be repeated in handlers.
Centralizing it reduces drift and makes event publication less error-prone.

### 2. The codebase already had aggregate event recording

The domain layer already recorded events through `RecordsDomainEvents`.
That made the persistence-boundary extraction approach a natural next step.

### 3. We wanted a Symfony/Doctrine equivalent of the explicit application context pattern

The chosen implementation is the pragmatic Symfony version of:

- collect events from tracked entities
- flush
- publish centrally

### 4. We still wanted explicit control, not hidden Doctrine subscriber magic

We did not move to a Doctrine subscriber hook yet.
Instead, we introduced an explicit `TransactionManager` abstraction with `flushAndPublish()`.

That keeps the flow centralized without hiding it too deeply.

## What is already in place

These pieces already support the future migration:

- `src/Layers/Domain/Shared/Event/DomainEvent.php`
- `src/Layers/Domain/Shared/Event/RecordsDomainEvents.php`
- `src/Layers/Application/Shared/Event/DomainEventDispatcher.php`
- `src/Layers/Application/Shared/Persistence/TransactionManager.php`
- `src/Layers/Infrastructure/Event/InMemoryDomainEventDispatcher.php`
- `src/Layers/Infrastructure/Event/DomainEventsPublisher.php`
- `src/Layers/Infrastructure/Persistence/DoctrineDomainEventsExtractor.php`
- `src/Layers/Infrastructure/Persistence/DoctrineTransactionManager.php`

This means the domain model records events internally and infrastructure now owns the central publish step.

## Tradeoff we are accepting

The current approach means:

- extraction depends on Doctrine scheduled entity state
- publication is still in-process and synchronous
- there is no outbox or durable retry model yet

We accept this because it gives us centralized publication now without forcing an outbox or Messenger design prematurely.

## Follow-up plan

Current follow-up direction:

1. keep recording events inside aggregates only
2. keep `flushAndPublish()` as the application-facing boundary
3. decide whether event handlers should move to Messenger
4. decide whether external/integration events require an outbox
5. separate domain events from integration events once external delivery becomes relevant

## Trigger to revisit this

Revisit the design when one or more of these become true:

- event handlers need async delivery
- external integrations are added
- reliable delivery becomes necessary
- multiple bounded contexts start consuming events differently
- we need retry, deduplication, or delivery auditing

## Short version

We moved domain event publication to a centralized `flushAndPublish()` flow because:

- it removes handler-level duplication
- it matches the aggregate-event pattern already present in the domain
- it gives us a clear Symfony/Doctrine equivalent of tracked-entity event publication
- it still keeps control explicit through a transaction manager abstraction

The next likely evolution is:

- Messenger-backed dispatch if async handling becomes useful
- outbox/integration event separation if reliability requirements increase
