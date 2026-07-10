# API Docs Setup

## Version

- Current version: `2.0`
- Last updated: `2026-07-10`

## Change Log

### 2.0

- moved documented controllers and models to the slice-first `Presentation` structure
- request payloads now live under `Presentation/Crm/Apartment/Models/Payload`
- response models now live under `Presentation/Crm/Apartment/Models/Response` and `Presentation/Internal/Health/Models/Response`
- Swagger docs now describe the new presentation slice layout

### 1.1

- aligned runtime controller responses with documented OpenAPI response models
- controllers now return response DTO objects instead of ad hoc normalized arrays for documented success payloads
- `HealthController` now returns `HealthResponse`
- `ApartmentController` now returns `ApartmentResponse` and `ApiMessageResponse`

### 1.0

- introduced NelmioApiDocBundle setup
- added Swagger UI and OpenAPI JSON routes
- documented `HealthController` and `ApartmentController`
- introduced request/response schema classes for API docs

## Decision

We are using `NelmioApiDocBundle` as the current API documentation setup for Symfony controllers.

For now, the setup is intentionally minimal and controller-centric.

## What was added

- `nelmio/api-doc-bundle`
- `symfony/twig-bundle`
- `symfony/asset`
- `twig/twig`

Manual Symfony wiring:

- `config/bundles.php`
- `config/packages/nelmio_api_doc.yaml`
- `config/routes/nelmio_api_doc.yaml`

Current docs routes:

- `/api/doc.json`
- `/api/doc`

## Current convention

We decided to document controllers with PHP attributes.

### Controller-level convention

For controller actions:

- use Symfony route attributes as usual
- add `OpenApi\Attributes` directly on the action
- use `Nelmio\ApiDocBundle\Attribute\Model` for request and response models

### Request model convention

Request DTOs stay in the controller request namespace and can be reused for docs.

Current example:

- `CreateApartmentPayload`

### Response model convention

We introduced explicit response schema classes for docs.

Current examples:

- `ApartmentResponse`
- `HealthResponse`
- `ApiMessageResponse`
- `MoneyResponse`
- `AmenityResponse`
- `AddressResponse`

These are now also the runtime response shapes for the documented controller responses.

## Why we chose this approach

### 1. The controller surface is still small

There are only a few endpoints right now, so documenting controllers directly is the fastest stable setup.

### 2. We want visible API documentation while building endpoints

The Swagger UI gives immediate feedback during controller work.

This is useful now because:

- route coverage is small
- request/response shapes are still evolving
- the team can inspect docs in the browser while implementing endpoints

### 3. We do not want a separate documentation abstraction yet

For now, putting OpenAPI attributes close to the controller keeps the documentation behavior obvious.

That is better than adding an extra doc-only layer too early.

## How to use it while implementing controllers

Typical workflow:

1. create or update the controller route
2. add OpenAPI attributes to the action
3. reference the request/response models
4. open `/api/doc`
5. verify the endpoint appears correctly in Swagger UI

## UI

Yes, there is a browser UI.

Current UI route:

- `/api/doc`

Generated OpenAPI JSON:

- `/api/doc.json`

## Scope of this first setup

This setup currently covers:

- `Presentation/Internal/Health/Controller/HealthController`
- `Presentation/Crm/Apartment/Controller/*`

It is a phase-1 setup, not the final API documentation design.

## Tradeoffs we are accepting

### 1. Response models are now part of runtime controller output

The documented response schema classes are also used as the actual JSON response objects for the documented endpoints.

That keeps runtime payloads aligned with the generated OpenAPI contract.

### 2. Error responses are still simple

Right now error payloads are still basic message objects.

We have not yet introduced a full common API error format.

### 3. Public API enums still reflect current implementation

For example, amenities are currently integer-backed in the API docs because the current controller input uses integer enum values.

If the public API later switches to string enums, this file must be updated.

## Follow-up decisions to revisit later

### 1. Unified error format

Define one reusable API error response structure for:

- validation errors
- domain errors
- not found
- unexpected failures

### 2. Tags and grouping

As the API grows, decide how to group docs:

- by controller
- by bounded context
- by business capability

### 3. Auth/security docs

When authentication is added, define security schemes in Nelmio config and apply them consistently to operations.

## Trigger to update this file

Update this file whenever one of these changes:

- documentation route location
- request/response documentation convention
- controller annotation strategy
- public API schema conventions
- error response structure
- security/auth documentation setup

## Short version

We chose Nelmio with Swagger UI because it gives immediate browser-visible documentation for Symfony controllers with minimal setup.

We are documenting controllers directly with OpenAPI attributes and using request/response model classes where needed.

This is intentionally pragmatic and should be revisited when the API surface grows.
