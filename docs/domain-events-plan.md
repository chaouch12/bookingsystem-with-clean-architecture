# Domain Events Plan

## Goal

Introduce a domain event pattern that keeps domain state changes inside aggregates, while allowing follow-up actions to run outside the aggregate without coupling everything together.

This plan is written for the current codebase shape:

- `backend/src/Layers/Domain/Appartment`
- `backend/src/Layers/Domain/Users`
- Symfony + Doctrine

The immediate target is in-process domain events. Do not start with framework events or external message brokers. First make the domain model publish events consistently, then decide later whether some handlers should become async.

## Why Use Domain Events Here

Use domain events when:

- one domain action should trigger multiple follow-up actions
- the aggregate should not know about infrastructure concerns
- you want better auditability around meaningful business changes
- you expect more workflows to grow around the same domain actions

Do not use domain events for:

- simple CRUD with no business consequence
- technical lifecycle hooks like "entity persisted"
- replacing normal method calls inside one aggregate

## Core Rule

An aggregate raises a domain event when something meaningful happened in the business, not when a setter was called.

Good:

- `UserRegistered`
- `UserRoleChanged`
- `ApartmentCreated`
- `ApartmentBooked`

Bad:

- `UserEmailSet`
- `ApartmentDescriptionUpdated` when that change has no downstream business meaning
- `DoctrineEntitySaved`

## Target Design

### 1. Domain Event Contract

Create a small event contract in the domain layer.

Suggested shape:

- `DomainEvent`
- `occurredOn(): \DateTimeImmutable`
- `eventName(): string`

Keep the first version minimal. Avoid inheritance trees unless they solve a real problem.

### 2. Aggregate Event Recording

Each aggregate that can publish events should record them internally.

Suggested pattern:

- `record(DomainEvent $event): void`
- `releaseEvents(): array`
- `domainEvents: list<DomainEvent>`

This keeps event creation inside the aggregate and event dispatch outside it.

### 3. Application-Level Dispatch

Dispatch events after the aggregate is successfully persisted.

For the first iteration:

- application service or use case saves aggregate
- repository/application layer releases events from aggregate
- dispatcher invokes in-process handlers

Do not dispatch directly from controller methods if you can avoid it. Put the orchestration in an application use case/service.

### 4. In-Process Handlers

Handlers should live outside the aggregate and perform follow-up actions such as:

- notifications
- audit log writes
- projections/read-model updates
- integration calls

Handlers should depend on interfaces where practical.

### 5. Later: Outbox Pattern

If you later need reliable async delivery to queues or external systems, add an outbox after the in-process model is stable.

Do not begin with RabbitMQ, Kafka, or Symfony Messenger transport unless you already have a concrete delivery requirement.

## Recommended Package Structure

Suggested structure under `backend/src`:

```text
Layers/
  Domain/
    Shared/
      Event/
        DomainEvent.php
        RecordsDomainEvents.php
    Users/
      Event/
        UserRegistered.php
        UserRoleChanged.php
        UserStatusChanged.php
    Appartment/
      Event/
        ApartmentCreated.php
        ApartmentUpdated.php
        ApartmentDeleted.php
  Application/
    Shared/
      Event/
        DomainEventDispatcher.php
    Users/
      Handler/
    Appartment/
      Handler/
  Infrastructure/
    Event/
      InMemoryDomainEventDispatcher.php
```

If you do not yet have a clear `Application` layer folder, create the shared event abstractions first and keep handlers close to the use cases that need them.

## Use Cases for This Project

Start with events that already have a believable business meaning in your current code.

### Users

#### Use Case: User Registered

Event:

- `UserRegistered`

Raised when:

- a new user is created for the first time

Useful handlers:

- write audit log entry
- send welcome email
- provision default settings or permissions

Payload should include:

- user id
- email
- role
- occurred on

#### Use Case: User Role Changed

Event:

- `UserRoleChanged`

Raised when:

- role changes from one valid role to another

Useful handlers:

- audit security-sensitive role changes
- invalidate permission caches
- notify admins for elevated access changes

Payload should include:

- user id
- old role
- new role
- occurred on

#### Use Case: User Status Changed

Event:

- `UserStatusChanged`

Raised when:

- status changes in a meaningful way such as active to blocked, or pending to active

Useful handlers:

- disable sessions
- notify support or compliance flow
- update read model for admin UI

Important note:

Your current `UserStatusType` appears to duplicate role values. Fix that model before relying on status-based events. A status enum should represent lifecycle state, not authorization role.

### Appartment

#### Use Case: Apartment Created

Event:

- `ApartmentCreated`

Raised when:

- a new apartment aggregate is first created

Useful handlers:

- audit entry
- search index update
- initial availability projection

Payload should include:

- apartment id
- address summary
- created timestamp

#### Use Case: Apartment Updated

Event:

- `ApartmentUpdated`

Raised when:

- a business-relevant apartment change occurs

Be selective. If every field edit emits a generic event, handlers become noisy. Either:

- emit one broad `ApartmentUpdated` for now, or
- emit more specific events later only when a real use case appears

Useful handlers:

- refresh read model
- invalidate cached listings

#### Use Case: Apartment Deleted

Event:

- `ApartmentDeleted`

Raised when:

- apartment is removed from active domain usage

Useful handlers:

- remove from search index
- clear projections
- write audit trail

#### Later Use Case: Apartment Booked

This is the strongest long-term candidate for domain events because booking has natural side effects.

Event:

- `ApartmentBooked`

Potential handlers:

- send confirmation
- update occupancy projection
- trigger payment flow
- trigger housekeeping workflow

If booking is added later, use it as the benchmark event for whether the pattern is working well.

## Implementation Phases

### Phase 1: Foundations

Deliverables:

- `DomainEvent` contract
- event recording support on aggregates
- simple in-process dispatcher interface
- one infrastructure dispatcher implementation

Acceptance criteria:

- aggregates can record and release events
- dispatcher can dispatch multiple events
- no framework-specific dependency leaks into domain entities

### Phase 2: First Real Aggregate

Start with `User` or `Appartment`, but only one first.

Recommendation:

- start with `User`

Reason:

- events like registration and role changes are easier to reason about than apartment CRUD

Deliverables:

- `UserRegistered`
- `UserRoleChanged`
- aggregate methods that record those events
- one or two trivial handlers such as audit logging

Acceptance criteria:

- event is raised only on meaningful transition
- handlers run after persistence
- unit tests cover event recording

### Phase 3: Move Write Logic Out of Controllers

Right now controller-driven persistence will make event orchestration awkward.

Deliverables:

- application services or use cases for user/apartment commands
- controllers become thin adapters
- event dispatch happens in application flow, not controller body

Acceptance criteria:

- controllers no longer coordinate domain details directly
- one command flow can persist aggregate and dispatch released events

### Phase 4: Add Appartment Events

Deliverables:

- `ApartmentCreated`
- `ApartmentUpdated` or narrower alternatives
- `ApartmentDeleted`
- handlers for audit/projection concerns

Acceptance criteria:

- apartment aggregate emits events consistently
- handlers do not contain business rules that belong in aggregate methods

### Phase 5: Reliability and Async Options

Only do this after real pressure appears.

Possible additions:

- outbox table
- retryable handlers
- idempotency keys
- Symfony Messenger integration

Acceptance criteria:

- no event is lost between transaction commit and handler execution
- handlers can be retried safely

## Testing Strategy

### Unit Tests

Test aggregate behavior:

- event is recorded when business transition happens
- event is not recorded when no meaningful change happened
- payload contains required domain data

### Application Tests

Test orchestration:

- aggregate is saved
- released events are dispatched once
- handlers are invoked in expected flow

### Integration Tests

Add later if needed:

- Doctrine transaction + dispatch boundary
- outbox persistence
- async processing

## Design Rules

Keep these rules strict:

1. Aggregates record events; they do not dispatch them.
2. Events describe the past, so name them in past tense.
3. Event payload should be immutable.
4. Handlers may fail independently; domain state should already be valid before handlers run.
5. Do not put repositories, mailers, or HTTP clients inside domain entities.
6. Do not create events for every setter.
7. Prefer explicit aggregate methods like `changeRole()` over generic state mutation when an event matters.

## Recommended First Slice

Implement this exact first slice:

1. Add shared domain event interfaces and recording support.
2. Refactor `User` away from generic setters toward explicit methods:
   - `register(...)`
   - `changeRole(...)`
   - `changeStatus(...)`
3. Raise:
   - `UserRegistered`
   - `UserRoleChanged`
4. Add a synchronous dispatcher.
5. Add one simple handler:
   - `WriteUserAuditLogOnRoleChanged`
6. Add unit tests for event recording.

This is small enough to finish without overbuilding and large enough to prove whether the pattern is useful in this codebase.

## Open Questions

Answer these before Phase 2 starts:

- Where should the application/use-case layer live in this repository?
- Do you want audit logging in database, file, or external system?
- Should handlers run inside the same transaction or only after commit?
- Is `UserStatusType` actually meant to represent lifecycle state?
- Will bookings become a separate aggregate soon?

## Definition of Done

The domain event pattern is considered established when:

- at least one aggregate records events
- at least one application flow dispatches them after persistence
- at least one handler performs a real follow-up action
- tests cover event recording and dispatch flow
- controllers are not the place where event coordination happens
