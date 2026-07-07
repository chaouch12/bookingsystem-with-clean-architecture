# Architecture Summary

## Goal

This document summarizes the current architecture decisions for the booking system so future changes follow one coherent model.

## High-Level Structure

The project follows layered architecture with explicit separation between:

- Presentation
- Application
- Domain
- Infrastructure/Persistence

## Domain Layer

The domain layer owns:

- entities / aggregates
- value objects
- domain services
- domain events
- domain-specific errors/results

Examples:

- `User`
- `Appartment`
- `Booking`
- `BookingPeriod`
- `GuestCount`
- `Money`

### Domain rules

- business invariants live in entities/value objects
- aggregates record domain events
- state transitions return domain results where appropriate

Examples:

- booking `confirm()`
- booking `reject()`
- booking `cancel()`

## Application Layer

The application layer owns:

- commands
- queries
- command/query handlers
- use-case orchestration
- event dispatch after persistence
- application messaging abstractions

Examples:

- `CreateBookingCommand`
- `CreateBookingCommandHandler`
- `Command`, `CommandHandler`
- `Query`, `QueryHandler`

### Application rules

- one use case -> one handler
- controllers should delegate to commands/queries
- handlers return `Result` / `ResultWithValue`
- handlers should not return aggregates when an id or DTO is enough

## Presentation Layer

The presentation layer owns:

- controllers
- request mapping
- HTTP response mapping

Controllers should:

1. translate HTTP request into command/query
2. invoke application layer
3. translate result into HTTP response

Controllers should not:

- contain business logic
- calculate pricing
- call domain transitions directly if a handler exists

## Infrastructure Layer

The infrastructure layer owns:

- Doctrine repositories
- event dispatcher implementations
- framework integration
- database-specific details

Examples:

- Doctrine repositories
- in-memory domain event dispatcher
- future Messenger-backed bus implementations

## CQRS Decision

We are following a CQRS-style split:

- Commands for writes
- Queries for reads

### Writes

Write side uses:

- command handlers
- aggregates
- domain methods
- repositories
- domain events

### Reads

Read side should use:

- query handlers
- DTO/read models
- optimized DB reads

Read side should avoid loading full aggregates when not needed.

Preferred approach:

- dedicated read repository for read-model access
- DBAL QueryBuilder as the default way to express read-model queries
- raw SQL only when QueryBuilder becomes a poor fit

Avoid:

- loading Doctrine aggregates on query side only to flatten them again into API DTOs

## Result/Error Decision

Expected application and domain failures should use:

- `Result`
- `ResultWithValue`
- typed `Error`

Examples:

- `BookingErrors::overlap()`
- `UserErrors::notFound()`
- `AppartmentErrors::notFound()`

Generic `RuntimeException` should not be used for expected business/application failures.

## Exception Decision

Use custom exceptions only for:

- unexpected runtime issues
- infrastructure failures
- exceptional application failures outside normal business flow

Use result failures instead of exceptions for:

- not found in normal use-case flow
- overlap/business conflict
- invalid state transition

## Doctrine Entity Decision

Doctrine entities are used as domain aggregates in this project, but with discipline.

Rules:

- keep aggregate boundaries explicit
- prefer ids across aggregate boundaries
- do not over-model object graphs with `ManyToOne` just because Doctrine allows it

Example:

`Booking` stores:

- `appartmentId`
- `guestUserId`

instead of object references to full `Appartment` / `User` aggregates.

Database foreign keys still exist at migration level.

Additional rule:

- aggregate repositories are primarily for write-side use cases
- query-side flat responses should prefer read-optimized access instead of aggregate hydration

## Booking Aggregate Decision

`Booking` is a separate aggregate root.

It owns:

- period
- guest count
- status transitions
- pricing snapshot
- booking domain events

It does not own:

- apartment entity graph
- user entity graph
- overlap query logic

Overlap checks live in repository/application orchestration.

## Pricing Decision

Pricing is calculated by a dedicated domain service:

- `BookingPricingService`

It returns:

- `PricingDetails`

This keeps pricing rules out of controllers and out of generic application glue.

## Domain Events Decision

Aggregates record domain events internally.

Application handlers:

1. persist aggregate
2. release events
3. dispatch them

Domain events are not the same thing as commands/queries.

Commands/queries drive use cases.
Domain events react after domain changes.

## Current Known Gaps

These are still expected follow-ups:

- introduce query side for booking reads
- add command/query buses, likely via Symfony Messenger
- clean remaining namespace drift in older files
- add migration sync for latest booking lifecycle fields
- normalize older exception helper patterns

## Summary

The architecture decisions currently are:

1. layered architecture
2. CQRS-style application layer
3. result/error-based expected failures
4. domain-driven aggregates and value objects
5. explicit domain events
6. ids instead of rich aggregate associations across boundaries
7. pricing/domain logic outside controllers

That is the baseline to preserve going forward.
