# Domain Events Decision

## Decision

For now, domain events stay published manually from application handlers after persistence.

Current pattern:

1. aggregate records domain events internally
2. handler saves the aggregate
3. handler calls `releaseDomainEvents()`
4. handler passes the events to `DomainEventDispatcher`

Example in the current codebase:

- `Booking` records events through `RecordsDomainEvents`
- `CreateBookingCommandHandler` saves the booking and then dispatches released events

## Why we are keeping it like this for now

We discussed moving to the more centralized approach shown in the screenshot:

- persist changes
- inspect tracked entities during/after flush
- collect domain events automatically
- publish them from one infrastructure place

That is a good direction, but we are intentionally not doing it yet.

Reasons:

### 1. The current codebase is still small

There are only a few handlers and only a small number of domain events.

Manual publication is still easy to follow and does not yet create much duplication.

### 2. We want the flow to remain explicit

Right now the event lifecycle is obvious in the application layer:

- create/update aggregate
- save it
- dispatch released events

That is simple to debug while the architecture is still evolving.

### 3. We do not yet have a transaction/outbox strategy

If we move event publication into Doctrine flush handling, the next questions become important immediately:

- publish before or after flush?
- what happens when an event handler fails?
- should event handlers be synchronous or async?
- do we need an outbox for reliability?

We have not made those decisions yet, so centralizing publication now would introduce infrastructure decisions too early.

### 4. We want to avoid half-finished infrastructure

Doing automatic dispatch through Doctrine without a clear reliability and failure model can leave the project in an awkward middle state:

- more hidden behavior
- more complex testing
- unclear transaction boundaries

For the current project stage, that complexity is not justified yet.

## What is already in place

These pieces already support the future migration:

- `src/Layers/Domain/Shared/Event/DomainEvent.php`
- `src/Layers/Domain/Shared/Event/RecordsDomainEvents.php`
- `src/Layers/Application/Shared/Event/DomainEventDispatcher.php`
- `src/Layers/Infrastructure/Event/InMemoryDomainEventDispatcher.php`

This means the domain model already records events correctly.
Only the publication mechanism is still manual.

## Tradeoff we are accepting

The current approach means:

- publication responsibility is still in handlers
- handlers must remember to dispatch released events
- duplicated save-then-dispatch flow may grow over time

We accept this for now because the explicitness is currently more valuable than the added infrastructure.

## Follow-up plan

When the number of handlers/events grows, revisit this and move to centralized publication.

Target direction:

1. keep recording events inside aggregates only
2. collect domain events from tracked Doctrine entities during or after flush
3. clear them once collected
4. publish them centrally through one infrastructure adapter
5. later decide whether to route them through Messenger and/or an outbox

## Trigger to revisit this

Revisit the design when one or more of these become true:

- several handlers repeat the same save-then-dispatch logic
- event handling becomes more important to the application flow
- external integrations are added
- async or reliable delivery becomes necessary
- we introduce a formal transaction boundary around application use cases

## Short version

We are keeping manual handler-level domain event publication for now because:

- it is explicit
- the codebase is still small
- the reliability model is not decided yet
- centralizing it now would add infrastructure earlier than needed

The future direction is still:

- Doctrine-based collection after persistence
- centralized dispatch
- possible Messenger/outbox integration later
