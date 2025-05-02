# Scribe JSON:API Attribute Reference

This document provides comprehensive documentation for all Scribe attribute classes used in the Laravel Doctrine JSON:API package. These attributes enable automatic, detailed, and accurate API documentation generation with [Scribe](https://scribe.knuckles.wtf/laravel/). Each section includes a description, usage guidelines, property details, and direct links to the class source files.

## Table of Contents

- [Overview](#overview)
- [Common Usage](#common-usage)
- [Attributes](#attributes)
  - [ResourceMetadata](#resourcemetadata)
  - [ResourceRequest](#resourcerequest)
  - [ResourceRequestList](#resourcerequestlist)
  - [ResourceRequestCreate](#resourcerequestcreate)
  - [ResourceRequestRelationships](#resourcerequestrelationships)
  - [ResourceResponse](#resourceresponse)
  - [ResourceResponseRelated](#resourceresponserelated)
  - [ResourceResponseRelationships](#resourceresponserelationships)
- [Best Practices](#best-practices)
- [See Also](#see-also)

## Overview

Scribe attributes are PHP 8+ attributes that you add to your controller methods or classes. They provide metadata about your JSON:API endpoints, such as resource type, grouping, expected request/response structure, and relationships. Scribe strategies scan for these attributes to generate accurate OpenAPI/Swagger documentation and beautiful docs UIs.

## Common Usage

- Place attributes directly above controller methods or classes.
- You can combine multiple attributes on a single method for full endpoint description.
- All attributes are in the `Sowl\JsonApi\Scribe\Attributes` namespace.

Example:

```php
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;
use Sowl\JsonApi\Scribe\Attributes\ResourceMetadata;

#[ResourceMetadata(groupName: 'Users', groupDescription: 'Operations related to user resources.')]
class UserController
{
    #[ResourceRequest(resourceType: 'users', idType: 'string', idExample: 'abc123')]
    #[ResourceResponse(resourceType: 'users', status: 200, description: 'Get a user by ID')]
    public function show($id) { /* ... */ }
}
```

## Attributes

### [ResourceMetadata](../src/Scribe/Attributes/ResourceMetadata.php)

**Purpose:**
Provides grouping and descriptive metadata for endpoints or resource classes. Used for grouping endpoints, customizing group names, descriptions, and authentication flags in documentation.

**Usage:**
- Place on controller classes or methods.

**Properties:**
- `groupName: string|null` — The group name for the endpoint or resource.
- `groupDescription: string|null` — Description for the group.
- `subgroup: string|null` — Subgroup name for further grouping.
- `subgroupDescription: string|null` — Description for the subgroup.
- `title: string|null` — Title of the endpoint or resource.
- `description: string|null` — Description of the endpoint or resource.
- `authenticated: bool` — Whether the endpoint requires authentication.

**Example:**
```php
#[ResourceMetadata(groupName: 'Users', groupDescription: 'Operations related to user resources.')]
class UserController {}
```

### [ResourceRequest](../src/Scribe/Attributes/ResourceRequest.php)

**Purpose:**
Marks a controller method as handling a single resource request (show, update, delete) for JSON:API.

**Usage:**
- Place on methods handling single-resource endpoints.

**Properties:**
- `resourceType: string|null` — JSON:API resource type.
- `idType: string|null` — Type of the resource identifier (e.g., string, int).
- `idExample: mixed` — Example value for the resource ID.
- `idParam: string` — Name of the route parameter for the resource ID (default: 'id').
- `acceptHeaders: array` — List of accepted content types (default: ['application/vnd.api+json']).

**Example:**
```php
#[ResourceRequest(resourceType: 'users', idType: 'string', idExample: 'abc123')]
public function show($id) {}
```

### [ResourceRequestList](../src/Scribe/Attributes/ResourceRequestList.php)

**Purpose:**
Marks a controller method as handling a resource collection (list) request for JSON:API.

**Usage:**
- Place on methods that return lists of resources.

**Properties:**
- `resourceType: string|null` — JSON:API resource type.
- `acceptHeaders: array` — List of accepted content types (default: ['application/vnd.api+json']).

**Example:**
```php
#[ResourceRequestList(resourceType: 'users')]
public function index() {}
```

### [ResourceRequestCreate](../src/Scribe/Attributes/ResourceRequestCreate.php)

**Purpose:**
Marks a controller method as handling resource creation for JSON:API.

**Usage:**
- Place on methods that create new resources.

**Properties:**
- `resourceType: string|null` — JSON:API resource type.
- `acceptHeaders: array` — List of accepted content types (default: ['application/vnd.api+json']).

**Example:**
```php
#[ResourceRequestCreate(resourceType: 'users')]
public function store($request) {}
```

### [ResourceRequestRelationships](../src/Scribe/Attributes/ResourceRequestRelationships.php)

**Purpose:**
Marks a controller method as handling relationship requests for a resource (e.g., `/users/{id}/relationships/{relation}`) in JSON:API.

**Usage:**
- Place on methods that manage relationships endpoints.

**Properties:**
- `resourceType: string|null` — JSON:API resource type.
- `idType: string|null` — Type of the resource identifier.
- `idExample: mixed` — Example value for the resource ID.
- `idParam: string` — Name of the route parameter for the resource ID (default: 'id').
- `acceptHeaders: array` — List of accepted content types (default: ['application/vnd.api+json']).

**Example:**
```php
#[ResourceRequestRelationships(resourceType: 'users', idType: 'string', idExample: 'abc123', idParam: 'id')]
public function relationships($id, $relationship) {}
```

### [ResourceResponse](../src/Scribe/Attributes/ResourceResponse.php)

**Purpose:**
Describes the response for a resource endpoint in JSON:API.

**Usage:**
- Place on methods to specify response details for a resource.

**Properties:**
- `resourceType: string|null` — JSON:API resource type.
- `status: int` — HTTP status code (default: 200).
- `description: string|null` — Description of the response.
- `fractalOptions: array` — Options for Fractal transformation.
- `collection: bool` — Whether the response is a collection.
- `pageNumber: int` — Example page number for paginated responses.
- `pageSize: int` — Example page size for paginated responses.
- `contentTypeHeaders: array` — List of content-type headers (default: ['application/vnd.api+json']).

**Example:**
```php
#[ResourceResponse(resourceType: 'users', status: 200, description: 'Get a user by ID')]
public function show($id) {}
```

### [ResourceResponseRelated](../src/Scribe/Attributes/ResourceResponseRelated.php)

**Purpose:**
Describes the response for a related resource endpoint (e.g., `/users/{id}/roles`) in JSON:API.

**Usage:**
- Place on methods returning related resources.

**Properties:**
- `resourceType: string|null` — JSON:API resource type.
- `relationshipName: string|null` — Name of the relationship.
- `status: int` — HTTP status code.
- `description: string|null` — Description of the response.
- `fractalOptions: array` — Options for Fractal transformation.
- `collection: bool` — Whether the response is a collection.
- `pageNumber: int` — Example page number.
- `pageSize: int` — Example page size.
- `contentTypeHeaders: array` — List of content-type headers (default: ['application/vnd.api+json']).

**Example:**
```php
#[ResourceResponseRelated(resourceType: 'users', relationshipName: 'roles', collection: true, description: 'Get related roles for a user')]
public function related($id, $relationship) {}
```

### [ResourceResponseRelationships](../src/Scribe/Attributes/ResourceResponseRelationships.php)

**Purpose:**
Describes the response for a relationships endpoint (e.g., `/users/{id}/relationships/roles`) in JSON:API.

**Usage:**
- Place on methods returning relationship data.

**Properties:**
- `resourceType: string|null` — JSON:API resource type.
- `relationshipName: string|null` — Name of the relationship.
- `status: int` — HTTP status code.
- `description: string|null` — Description of the response.
- `fractalOptions: array` — Options for Fractal transformation.
- `collection: bool` — Whether the response is a collection.
- `pageNumber: int` — Example page number.
- `pageSize: int` — Example page size.
- `contentTypeHeaders: array` — List of content-type headers (default: ['application/vnd.api+json']).

**Example:**
```php
#[ResourceResponseRelationships(resourceType: 'users', relationshipName: 'roles', collection: true, description: 'Get user roles relationship')]
public function relationships($id, $relationship) {}
```

## Best Practices

- Always annotate your endpoints with the most specific attribute(s) for best documentation results.
- Use `ResourceMetadata` for grouping and descriptions to improve docs navigation.
- Use real resource types and relationship names for clarity and accuracy.
- Keep attribute property values up-to-date with your actual API implementation.
- Combine request and response attributes on the same method for complete endpoint documentation.

## See Also

- [Scribe Documentation](https://scribe.knuckles.wtf/laravel/)
- [JSON:API Specification](https://jsonapi.org/format/)
- [Laravel Doctrine JSON:API Scribe Strategies](../src/Scribe/Strategies)
- [Main Scribe Documentation](./Scribe.md)

> **For further details and advanced usage, see the PHP docblocks in each attribute class and refer to this file from the main [Scribe documentation](./Scribe.md).**
