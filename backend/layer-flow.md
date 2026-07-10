# Layer Flow

## Version

- Current version: `1.0`
- Last updated: `2026-07-10`

## Change Log

### 1.0

- introduced the current layer-flow note
- documented the intended DDD-oriented flow between presentation, application, domain, and infrastructure
- recorded domain principles, structure, patterns, and cross-domain communication rules

## Layer Flow

Current intended dependency and orchestration flow:

```text
Presentation -> Application -> Domain
                         |
                         v
                  Infrastructure
```

More concretely:

```text
Presentation
  -> maps transport payloads to application commands/queries
  -> calls application handlers
  -> maps application views/results to response models

Application
  -> orchestrates use cases
  -> coordinates repositories, transactions, validation, domain event publication
  -> does not contain transport concerns

Domain
  -> owns business rules, invariants, entities, value objects, domain events
  -> does not depend on presentation or infrastructure details

Infrastructure
  -> implements persistence and framework adapters
  -> supports application and domain contracts
```

## Boundary Rules

Allowed direction:

- `Presentation -> Application`
- `Application -> Domain`
- `Application -> Infrastructure abstractions/adapters through contracts`

Disallowed direction:

- `Presentation -> Domain repository directly`
- `Presentation -> Doctrine or persistence details directly`
- `Domain -> Presentation`
- `Domain -> Framework-specific infrastructure behavior`

## Domain Principles

### 1. Domain owns business truth

Business rules belong in:

- entities
- value objects
- domain services
- domain events

They should not live in controllers or persistence adapters.

### 2. Invariants stay in the domain

If something must never be invalid in the business model, it must be enforced in the domain model itself.

Examples:

- invalid booking periods
- invalid guest counts
- illegal booking state transitions

### 3. Application orchestrates, domain decides

Application handlers coordinate use cases.
Domain objects decide whether a business action is valid.

### 4. Presentation stays transport-focused

Presentation accepts input and returns output.
It should not contain business orchestration or repository usage.

## Domain Structure

Current intended shape:

```text
Layers/
  Domain/
    Appartment/
    Booking/
    Users/
    Shared/
```

Each domain slice should contain only the business concepts needed for that slice.

### Key Components

#### Entities / Aggregates

Examples:

- `Booking`
- `Appartment`
- `User`

Responsibilities:

- hold business state
- enforce invariants
- expose business behavior
- record domain events when meaningful changes happen

#### Value Objects

Examples:

- `BookingPeriod`
- `GuestCount`
- `Money`
- `Email`
- `Name`
- `Address`

Responsibilities:

- model validated business concepts
- be small, self-validating, and behavior-oriented

#### Domain Events

Examples:

- `BookingCreated`
- `BookingConfirmed`
- `UserRegistered`

Responsibilities:

- represent meaningful business facts that already happened
- stay inside domain language

#### Repositories

Domain repositories represent collection-style access needed by application/domain flows.

Important rule:

- presentation must not use them directly

#### Domain Services

Use them when business logic:

- does not naturally belong to one entity/value object
- is still domain logic

Example:

- pricing calculation

## Domain Patterns

### 1. Aggregate recording domain events

Pattern:

- aggregate performs business action
- aggregate records domain event internally
- event is published later by the application/persistence boundary

Current project direction:

- entities record events through `RecordsDomainEvents`
- infrastructure/application transaction flow publishes them centrally

### 2. Command / Query split in application

Pattern:

- commands mutate state
- queries read state

Why:

- keeps write and read concerns clearer
- works well with DDD slices

### 3. Thin presentation + mapper

Pattern:

- payload -> application command/query
- application result/view -> response model

Why:

- keeps controllers small
- prevents presentation from drifting into domain orchestration

### 4. Explicit transaction boundary

Pattern:

- application persists aggregate changes
- transaction/persistence boundary performs flush and domain event publication

Why:

- centralizes persistence-side orchestration
- avoids handler-level event publication duplication

### 5. Validation at application boundary

Pattern:

- validate commands/queries before application logic runs

Why:

- fail early on invalid transport/application input
- keep domain invariants separate from transport validation

## Cross-Domain Communication

Cross-domain communication should happen carefully and explicitly.

### Preferred approach

1. application handler loads required domain objects/repositories
2. one domain object performs its business operation
3. if needed, emit domain events
4. other parts react through handlers/listeners

### Rules

- do not let one domain slice reach deeply into another slice’s internals
- prefer communication through:
  - application orchestration
  - domain events
  - small shared concepts only when truly necessary

### Use domain events when

- one business event should trigger follow-up work elsewhere
- the originating aggregate should not know about all downstream actions

### Avoid

- controller-mediated cross-domain coordination
- presentation layer calling multiple domain repositories directly
- leaking infrastructure concerns into domain communication

## Current Practical Reading

For this repo today:

- `Presentation` is moving toward slice-first CRM/internal structure
- `Application` owns apartment and booking use-case orchestration
- `Domain` owns booking/appartment/users business logic
- `Infrastructure` owns Doctrine, persistence extraction, and event publication adapters

## Trigger to update this file

Update this file whenever one of these changes:

- dependency direction between layers
- presentation/application/domain responsibilities
- domain event publication model
- repository boundary rules
- cross-domain communication strategy

## Short Version

The intended architecture is:

- `Presentation` handles transport only
- `Application` orchestrates use cases
- `Domain` owns business rules
- `Infrastructure` implements technical details

DDD rule of thumb:

- controllers should not do domain work
- application should not become transport-aware
- domain should not become framework-aware
