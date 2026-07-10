# Validator Explained

## Version

- Current version: `1.1`
- Last updated: `2026-07-10`

## Change Log

### 1.1

- added validation-backed apartment application commands and queries
- apartment create/update/delete/get/list handlers now validate application messages at handler entry

### 1.0

- introduced Symfony Validator-based validation for current commands, queries, and selected DTOs
- added `MessageValidator`
- validated messages at handler entry

## What was added

The application now uses Symfony Validator for the current command, query, and DTO surface.

Implemented pieces:

- `src/Layers/Application/Shared/Validation/MessageValidator.php`
- validation attributes on application messages and DTOs
- handler-level validation at the start of each handler

Current handlers using it:

- `CreateApartmentCommandHandler`
- `UpdateApartmentCommandHandler`
- `DeleteApartmentCommandHandler`
- `GetApartmentQueryHandler`
- `ListApartmentsQueryHandler`
- `CreateBookingCommandHandler`
- `GetBookingQueryHandler`
- `SearchApartmentQueryHandler`

## Why this approach

This codebase does not currently have a message bus or middleware pipeline.

Because of that, the simplest coherent approach is:

1. define validation rules on the message/DTO class
2. validate at the application boundary
3. stop invalid input before repository or domain work begins

That keeps validation centralized without adding infrastructure the project does not use yet.

## What is validated now

### Commands and queries

- `CreateBookingCommand`
  - `appartmentId` must be positive
  - `guestUserId` must be positive
  - `period` must not be null
  - `guestCount` must not be null

- `CreateApartmentCommand`
  - required apartment fields must be present
  - basic string/length rules are enforced before application logic runs

- `UpdateApartmentCommand`
  - `id` must be positive
  - required apartment fields must be present

- `DeleteApartmentCommand`
  - `id` must be positive

- `GetApartmentQuery`
  - `id` must be positive

- `GetBookingQuery`
  - `bookingId` must be positive

- `SearchApartmentQuery`
  - `startDate` must not be null
  - `endDate` must not be null
  - `startDate <= endDate`

### DTOs

- `BookingResponse`
- `SearchApartmentResponse`
- `AddressDto`

These now have basic structural constraints such as positive IDs and non-blank strings.

## Runtime behavior

Handlers now call `MessageValidator` first.

If validation fails:

- a `ValidationFailedException` is thrown
- repository access should not happen
- downstream application logic is skipped

## Important behavior change

`SearchApartmentQueryHandler` used to return an empty successful result when:

- `startDate > endDate`

It now fails validation instead.

That is intentional. Invalid input is now treated as invalid input, not as a valid empty search.

## What this is not

This is not a full pipeline or mediator implementation.

No bus middleware was introduced.

That would only make sense if the project later grows into:

- many commands and queries
- shared cross-cutting behaviors
- a real dispatch abstraction

For the current size of the codebase, handler-entry validation is enough.

## Recommended next steps

### 1. Standardize error mapping

Decide how `ValidationFailedException` should be translated for:

- HTTP controllers
- CLI commands
- future message consumers

Common options:

- map to `400 Bad Request` in HTTP
- map to console error output in CLI
- optionally convert violations into the project `Result`/`ResultWithValue` style

### 2. Decide whether responses should be validated automatically

Right now response DTOs have constraints, but they are not automatically validated on every creation path.

Decide whether to:

- keep them as documentation plus optional validation
- validate read models in selected places
- avoid response validation entirely and rely on mapping tests

### 3. Add custom constraints only where needed

Do not create many custom validators too early.

Only add them when rules are:

- cross-field
- reused
- awkward to express with built-in constraints

### 4. Consider a shared application boundary later

If the project introduces a real command/query dispatch layer, move validation there so handlers no longer call `MessageValidator` directly.

At that point, a real validation pipeline would make sense.

## Rule of thumb

Use Symfony Validator for:

- transport/input validation
- DTO/message consistency
- cross-field application boundary checks

Use domain objects for:

- invariants
- business rules
- state transitions

Do not move domain invariants into Symfony Validator only.

## Files to revisit later

- `src/Layers/Application/Shared/Validation/MessageValidator.php`
- `src/Layers/Application/Booking/CreateBooking/CreateBookingCommand.php`
- `src/Layers/Application/Booking/GetBooking/GetBookingQuery.php`
- `src/Layers/Application/Apartments/SearchApartments/SearchApartmentQuery.php`
- handler tests under `tests/Layers/Application/*`

## Update rule

If the validation approach changes later, update this file instead of creating a separate competing note.

Examples:

- move validation to a message bus pipeline
- change exception-to-error mapping
- remove DTO-level validation
- add validation groups or custom constraint strategy
