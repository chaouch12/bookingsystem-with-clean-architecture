# CQRS Messaging Guide

## Purpose

This document explains the difference between command messaging and query messaging in this project, when to use each one, and how they fit into the application layer.

## Core Rule

- Commands change state.
- Queries read state.

Do not mix them.

## Command Messaging

### What a command is

A command is an application request that asks the system to perform a state-changing use case.

Examples:

- `CreateBookingCommand`
- `ConfirmBookingCommand`
- `RejectBookingCommand`
- `CancelBookingCommand`
- `RegisterUserCommand`
- `ChangeUserRoleCommand`

### What a command handler does

A command handler orchestrates a use case.

Typical responsibilities:

1. load required aggregates/entities from repositories
2. validate preconditions that require repository access
3. invoke domain methods
4. save changes
5. dispatch released domain events
6. return a result wrapper with success value or failure error

### What a command handler should return

Command handlers should not normally return aggregates.

Preferred return types:

- `ResultWithValue<int>` for created ids
- `ResultWithValue<string>` for reference numbers
- `ResultWithValue<SomeResultDto>` for small application results
- `Result` when only success/failure matters

Avoid returning:

- full Doctrine entities
- domain aggregates

Reason:

- application layer should expose use-case results, not internal domain objects

### When to use a command

Use a command when the request:

- creates something
- updates something
- deletes something
- confirms/rejects/cancels something
- triggers domain events

### Booking command use cases

- `CreateBookingCommand`
- `ConfirmBookingCommand`
- `RejectBookingCommand`
- `CancelBookingCommand`

Expected flow:

```text
Controller -> Command -> CommandHandler -> Repository -> Domain -> Save -> Domain Events
```

## Query Messaging

### What a query is

A query is an application request that asks the system to return data without changing state.

Examples:

- `GetBookingByIdQuery`
- `ListBookingsForApartmentQuery`
- `ListBookingsForUserQuery`
- `GetApartmentByIdQuery`
- `ListApartmentsQuery`

### What a query handler does

A query handler reads data and maps it into response models.

Typical responsibilities:

1. execute DB query or repository read
2. shape the response model
3. return result wrapper with data or failure error

For this project, query handlers may read directly through DBAL or through a dedicated read repository when the response shape is flat and does not require aggregate behavior.

### What a query handler should return

Preferred return types:

- `ResultWithValue<BookingView>`
- `ResultWithValue<list<BookingView>>`
- `ResultWithValue<ApartmentView>`

Query handlers should return:

- DTOs
- read models
- lists
- projections

They should not return:

- mutable aggregates for caller-side mutation

They also should not be forced through aggregate repositories when that only adds Doctrine hydration overhead before flattening data again.

### When to use a query

Use a query when the request:

- fetches one record
- fetches a list
- returns a calendar view
- returns dashboard/admin data
- does not modify state

### Booking query use cases

- `GetBookingByIdQuery`
- `ListBookingsForApartmentQuery`
- `ListBookingsForUserQuery`
- `GetApartmentAvailabilityQuery`

Expected flow:

```text
Controller -> Query -> QueryHandler -> Read Query/Repository -> DTO/View -> Response
```

## Differences Between Command and Query Messaging

| Topic | Command | Query |
|---|---|---|
| Purpose | Change state | Read state |
| Uses domain methods | Yes | Usually no |
| Uses domain events | Yes | No |
| Return value | Result / ResultWithValue of id or result DTO | ResultWithValue of read DTO(s) |
| Transactional | Usually yes | Usually no |
| Repository usage | Load/save aggregates | Read optimized access |
| Side effects | Yes | No |

## Read Model Access Decision

In this codebase:

- command side should use aggregate repositories
- query side may use DBAL/raw SQL directly
- if query logic grows, introduce dedicated read repositories

Reason:

- read responses are often flat DTOs
- aggregate hydration is unnecessary on the query side
- joins/projections/filtering are easier to express directly for reads

So the preferred progression is:

1. simple query handler with DBAL
2. dedicated read repository if SQL grows
3. keep aggregate repositories focused on write-side loading/saving

## Layer Placement

Both commands and queries belong in the application layer.

Suggested structure:

```text
backend/src/Layers/Application/
  Booking/
    Commands/
      CreateBooking/
      ConfirmBooking/
      RejectBooking/
      CancelBooking/
    Queries/
      GetBookingById/
      ListBookingsForApartment/
      ListBookingsForUser/
  Shared/
    Messaging/
      Command.php
      CommandHandler.php
      Query.php
      QueryHandler.php
```

## Result Pattern

This project uses result-based messaging on the application side.

Expected business/application failures should be returned as:

- `Result::failure(...)`
- `ResultWithValue::failureWithError(...)`

Examples:

- apartment not found
- user not found
- booking overlap
- invalid state transition

Unexpected failures may still raise exceptions, but expected use-case outcomes should prefer result wrappers.

## Design Rules

1. One command maps to one command handler.
2. One query maps to one query handler.
3. Controllers should send commands/queries, not orchestrate repositories directly.
4. Commands should not return aggregates.
5. Queries should return DTO/read models, not domain entities intended for mutation.
6. Query handlers must not mutate state.
7. Command handlers may dispatch domain events after persistence.

## Current Example

Current write-side example:

- `CreateBookingCommand`
- `CreateBookingCommandHandler`

The handler:

- loads apartment and user
- checks overlap
- uses pricing service
- creates booking
- saves booking
- dispatches domain events
- returns `ResultWithValue<int>` with the booking id

That is the intended pattern going forward.
