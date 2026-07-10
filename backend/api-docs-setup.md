# API Docs Setup

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

- `CreateApartmentRequest`

### Response model convention

We introduced explicit response schema classes for docs.

Current examples:

- `ApartmentResponse`
- `HealthResponse`
- `ApiMessageResponse`
- `MoneyResponse`
- `AmenityResponse`
- `AddressResponse`

These are primarily documentation models for now.

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

- `HealthController`
- `ApartmentController`

It is a phase-1 setup, not the final API documentation design.

## Tradeoffs we are accepting

### 1. Some response classes are docs-first classes

The response schema classes are mainly there to stabilize documentation shape.

Runtime controller responses still return normalized arrays.

That is acceptable for now because it avoids a larger serializer refactor.

### 2. Error responses are still simple

Right now error payloads are still basic message objects.

We have not yet introduced a full common API error format.

### 3. Public API enums still reflect current implementation

For example, amenities are currently integer-backed in the API docs because the current controller input uses integer enum values.

If the public API later switches to string enums, this file must be updated.

## Follow-up decisions to revisit later

### 1. Standard response DTOs at runtime

Decide whether controllers should eventually return dedicated response DTOs instead of arrays.

### 2. Unified error format

Define one reusable API error response structure for:

- validation errors
- domain errors
- not found
- unexpected failures

### 3. Tags and grouping

As the API grows, decide how to group docs:

- by controller
- by bounded context
- by business capability

### 4. Auth/security docs

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
