# Presentation Structure Decision

## Version

- Current version: `1.0`
- Last updated: `2026-07-10`

## Change Log

### 1.0

- removed the generic `Presentation/Http` structure
- adopted a slice-first DDD-oriented presentation structure
- moved apartment endpoints under `Presentation/Crm/Apartment`
- moved health endpoint under `Presentation/Internal/Health`
- established the rule that presentation must talk to application, not domain repositories

## Decision

We are organizing the presentation layer by interface area and business slice, not by transport folder names such as `Http`.

Current direction:

```text
Presentation/
  Crm/
    Apartment/
      Controller/
      Models/
        Payload/
        Response/
      Mapper/
  Internal/
    Health/
      Controller/
      Models/
        Response/
```

## Boundary rule

Presentation must orchestrate transport only.

Allowed dependency direction:

- `Presentation -> Application`

Disallowed direct dependency direction:

- `Presentation -> Domain repository`
- `Presentation -> aggregate construction for business orchestration`
- `Presentation -> infrastructure persistence details`

## Why we chose this structure

### 1. It matches DDD slice thinking better than `Http/...`

The important axis is not transport naming but business/interface area and use-case slice.

### 2. It scales better as CRM areas grow independently

`Apartment`, `Booking`, `Customer`, and similar areas can evolve without collapsing into one large controller bucket.

### 3. It keeps payload/response models local to the slice

Transport models should live close to the controllers and mappers that use them.

### 4. It makes boundary violations easier to spot

If a controller under `Presentation/Crm/Apartment` starts using a domain repository directly, that is now obviously the wrong dependency.

## Current orchestration rule

Controllers should:

1. receive payloads
2. map payloads to application commands/queries
3. call application handlers
4. map application views/results to response models
5. return JSON

Controllers should not:

- build domain aggregates directly for business flow
- mutate entities directly
- call domain repositories directly

## Current apartment slice

Apartment now uses:

- application commands/queries and handlers under `Application/Apartments/...`
- presentation mapper under `Presentation/Crm/Apartment/Mapper`
- payload and response models under `Presentation/Crm/Apartment/Models`

## Tradeoffs we are accepting

### 1. More files per slice

This structure is more explicit and therefore more verbose.

We accept that because the boundaries are clearer.

### 2. More mapper code

Moving mapping out of controllers introduces mapper classes.

We accept that because it prevents controllers from turning into application/domain orchestration code.

## Follow-up directions

### 1. Keep new presentation slices consistent

Future slices such as `Booking` or `Customer` should follow the same pattern.

### 2. Keep application use cases aligned

Presentation slices should map cleanly to application use-case folders.

Example:

- `Presentation/Crm/Apartment/...`
- `Application/Apartments/CreateApartment/...`
- `Application/Apartments/GetApartment/...`

### 3. Standardize API error mapping later

We are not adding a global exception subscriber yet.
Controllers still map simple failures explicitly.

## Trigger to update this file

Update this file whenever one of these changes:

- presentation folder structure
- allowed dependency boundaries for presentation
- mapper role within presentation
- decision about using or not using area folders like `Crm` / `Internal`

## Short version

We removed `Presentation/Http` and adopted a DDD-oriented slice structure.

Presentation is now organized by interface area and domain slice, and it must depend on application handlers rather than domain repositories.
