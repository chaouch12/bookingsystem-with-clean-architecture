# Booking Model Plan

## Goal

Model `Booking` as a separate domain aggregate that coordinates reservations for an `Appartment` by a `User`, without coupling the aggregates too tightly.

## Core Decision

`Booking` should be its own aggregate root.

Do not embed bookings inside `Appartment` or `User`.

Reason:

- booking has its own lifecycle
- booking has its own invariants
- booking has its own events
- booking queries usually span many bookings for one apartment

## Aggregate Boundary Diagram

```mermaid
graph TD
    U[User Aggregate]
    A[Appartment Aggregate]
    B[Booking Aggregate]

    B -->|references by userId| U
    B -->|references by appartmentId| A
```

## Domain Concept Diagram

```mermaid
classDiagram
    class Booking {
        +int id
        +int appartmentId
        +int guestUserId
        +BookingPeriod period
        +GuestCount guestCount
        +BookingStatus status
        +Money nightlyPriceSnapshot
        +Money cleaningFeeSnapshot
        +Money totalPrice
        +DateTimeImmutable createdAt
        +DateTimeImmutable confirmedAt
        +DateTimeImmutable cancelledAt
        +create()
        +confirm()
        +cancel()
    }

    class BookingPeriod {
        +DateTimeImmutable checkIn
        +DateTimeImmutable checkOut
        +nights() int
    }

    class GuestCount {
        +int value
    }

    class BookingStatus {
        <<enumeration>>
        PENDING
        CONFIRMED
        CANCELLED
        COMPLETED
    }

    class Appartment {
        +int id
        +Money price
        +Money cleaningFee
        +DateTimeImmutable lastBookedOnUtc
    }

    class User {
        +int id
        +Email email
        +Name name
    }

    Booking --> BookingPeriod
    Booking --> GuestCount
    Booking --> BookingStatus
    Booking ..> Appartment : appartmentId
    Booking ..> User : guestUserId
```

## Relationship Model

```mermaid
erDiagram
    USER ||--o{ BOOKING : places
    APPARTMENT ||--o{ BOOKING : reserved_for

    USER {
        int id PK
        string user_email
        string user_name
        string user_role
        string user_status
    }

    APPARTMENT {
        int id PK
        string name
        decimal price_amount
        string price_currency
        decimal cleaning_fee_amount
        string cleaning_fee_currency
        datetime last_booked_on_utc
    }

    BOOKING {
        int id PK
        int appartment_id FK
        int guest_user_id FK
        date check_in
        date check_out
        int guest_count
        string status
        decimal nightly_price_amount
        string nightly_price_currency
        decimal cleaning_fee_amount
        string cleaning_fee_currency
        decimal total_price_amount
        string total_price_currency
        datetime created_at
        datetime confirmed_at
        datetime cancelled_at
    }
```

## Dependency Graph

```mermaid
graph LR
    C[CreateBooking Use Case]
    BR[BookingRepository]
    AR[AppartmentRepository]
    UR[UserRepository]
    AV[Availability Check]
    PR[Price Calculation]
    B[Booking Aggregate]
    D[Domain Event Dispatcher]
    E[BookingCreated Event]

    C --> AR
    C --> UR
    C --> BR
    C --> AV
    C --> PR
    C --> B
    C --> D
    B --> E
```

## Booking Lifecycle

```mermaid
stateDiagram-v2
    [*] --> PENDING : create booking
    PENDING --> CONFIRMED : confirm
    PENDING --> CANCELLED : cancel
    CONFIRMED --> CANCELLED : cancel if policy allows
    CONFIRMED --> COMPLETED : stay finished
    CANCELLED --> [*]
    COMPLETED --> [*]
```

## Creation Flow

```mermaid
sequenceDiagram
    participant API as Controller/API
    participant UC as CreateBookingUseCase
    participant AR as AppartmentRepository
    participant UR as UserRepository
    participant BR as BookingRepository
    participant BA as Booking Aggregate
    participant ED as Event Dispatcher

    API->>UC: createBooking(command)
    UC->>AR: load apartment
    UC->>UR: load user
    UC->>BR: check overlap for apartment + period
    UC->>BA: create(period, guestCount, priceSnapshot)
    UC->>BR: save(booking)
    UC->>BA: releaseDomainEvents()
    UC->>ED: dispatch(events)
```

## Aggregate Responsibilities

### Booking Aggregate

Owns:

- booking lifecycle
- booking period invariant
- guest count invariant
- booking status transitions
- price snapshot stored at booking time
- booking domain events

Should not own:

- loading apartments
- loading users
- checking overlaps in repository
- sending emails
- updating external systems

### Appartment Aggregate

Owns:

- apartment listing/configuration
- current pricing defaults
- amenities
- address

Should not own:

- collection of booking entities as aggregate state
- booking lifecycle rules

### User Aggregate

Owns:

- identity
- role
- status

Should not own:

- booking lifecycle

## Recommended Dependencies Between Aggregates

Use identity references across aggregate boundaries:

- `Booking.appartmentId`
- `Booking.guestUserId`

Do not store:

- `Booking -> Appartment object`
- `Booking -> User object`

Reason:

- simpler persistence
- clearer boundaries
- no accidental aggregate graph loading
- repository queries remain explicit

## Suggested Value Objects

### `BookingPeriod`

Fields:

- `checkIn`
- `checkOut`

Rules:

- `checkOut` must be after `checkIn`
- stay length must be at least one night

### `GuestCount`

Fields:

- `value`

Rules:

- must be greater than zero
- optionally compare later to apartment capacity

### `BookingPriceSnapshot`

Fields:

- nightly/base amount
- cleaning fee
- total
- currency

Purpose:

- preserve historical price at booking time
- avoid recalculating old bookings from current apartment price

## Recommended Status Enum

```text
BookingStatus
- PENDING
- CONFIRMED
- CANCELLED
- COMPLETED
```

Keep this small until real workflow pressure appears.

## Recommended First Use Cases

### 1. CreateBooking

Inputs:

- `appartmentId`
- `guestUserId`
- `checkIn`
- `checkOut`
- `guestCount`

Responsibilities:

- load apartment
- load user
- validate period
- check overlap
- calculate snapshot price
- create booking
- persist booking
- dispatch `BookingCreated`

### 2. ConfirmBooking

Responsibilities:

- load booking
- validate current state
- transition `PENDING -> CONFIRMED`
- dispatch `BookingConfirmed`

### 3. CancelBooking

Responsibilities:

- load booking
- validate cancellation rule
- transition to `CANCELLED`
- dispatch `BookingCancelled`

## Recommended Events

```mermaid
graph TD
    BC[BookingCreated]
    BF[BookingConfirmed]
    BX[BookingCancelled]

    BC --> Audit[Audit Log]
    BC --> Notify[Send Confirmation Request or Email]
    BF --> Occupancy[Update Occupancy Projection]
    BF --> Apartment[Update Appartment lastBookedOnUtc if kept]
    BX --> Release[Release Availability Projection]
```

## Persistence Recommendation

For the first version:

- Doctrine entity for `Booking`
- separate `BookingRepository`
- overlap query at repository level

Useful repository methods:

```php
existsOverlapForAppartment(int $appartmentId, BookingPeriod $period): bool
save(Booking $booking, bool $flush = false): void
find(int $id): ?Booking
findByAppartment(int $appartmentId): array
findByUser(int $userId): array
```

## Implementation Order

1. `BookingStatus` enum
2. `BookingPeriod` value object
3. `GuestCount` value object
4. `Booking` aggregate
5. `BookingRepository`
6. `BookingCreated` event
7. `CreateBooking` use case
8. overlap query
9. tests

## Important Open Questions

Before implementation, decide:

1. Is booking always tied to a registered user?
2. Do staff create bookings on behalf of guests?
3. Should `confirmed` exist, or is create immediately confirmed?
4. Are overlapping `PENDING` bookings allowed?
5. Should apartment pricing be snapshotted at booking time?
6. Should `Appartment.lastBookedOnUtc` stay once booking exists?

## Recommended First Slice

Build this first:

- `Booking`
- `BookingPeriod`
- `GuestCount`
- `BookingStatus`
- `BookingCreated`
- `BookingRepository::existsOverlapForAppartment()`
- `CreateBooking` application service

Keep payment, refunds, and async notifications out of the first slice.
