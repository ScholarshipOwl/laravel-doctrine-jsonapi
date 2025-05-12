# Crafting JSON:API Responses

Sending correctly formatted responses is just as important as handling requests in a JSON:API compliant manner. The specification dictates a precise structure involving top-level keys like `data`, `errors`, `meta`, `links`, and `included`.

This package provides a dedicated `Sowl\JsonApi\Response\ResponseFactory` service to abstract away the complexities of building these structures, ensuring your API returns valid JSON:API documents with the correct HTTP status codes.

**On this page:**

-   [The Role of the ResponseFactory](#the-role-of-the-responsefactory)
-   [Accessing the ResponseFactory](#accessing-the-responsefactory)
-   [Common Response Types](#common-response-types)
    -   [Single Resource (`item`)](#single-resource-item)
    -   [Resource Collection (`collection`)](#resource-collection-collection)
    -   [Successful Creation (`created`)](#successful-creation-created)
    -   [Successful Deletion (`noContent`)](#successful-deletion-nocontent)
    -   [Meta-Only Responses (`meta`)](#meta-only-responses-meta)
    -   [Error Responses](#error-responses)
-   [How Transformers are Used](#how-transformers-are-used)
-   [Best Practices](#best-practices)

---

## The Role of the ResponseFactory

The `ResponseFactory` is your primary tool for generating JSON:API responses. It takes your Doctrine entities (or collections of entities) and, using the appropriate **Transformers**, builds the final JSON:API document structure. It also handles setting the correct HTTP status codes and `Content-Type` header (`application/vnd.api+json`).

## Accessing the ResponseFactory

Within your Action classes (which extend `Sowl\JsonApi\Action\AbstractAction`), the `ResponseFactory` is readily available via the `$this->response()` method:

```php
<?php

namespace App\Http\Actions;

use Sowl\JsonApi\Action\AbstractAction;
use Sowl\JsonApi\Response\Response; // Laravel Response class alias

class ShowUserAction extends AbstractAction
{
    public function handle(string $userId): Response
    {
        $user = $this->rm()->findOrFail(User::getResourceType(), $userId);

        // Use the response factory to return the item
        return $this->response()->item($user);
    }
}
```

If you need it elsewhere (e.g., custom controllers, services), you can inject `Sowl\JsonApi\Response\ResponseFactory` via Laravel's service container.

> Or just use `response()->item(...)`

## Common Response Types

The `ResponseFactory` offers convenient methods for common JSON:API response scenarios:

### Single Resource (`item`)

Used when returning a single resource object. Typically used for `Show`, `Update` operations, or sometimes `Create` if not using the `created()` helper. Sets HTTP status `200 OK` by default.

```php
// Inside an Action's handle method

// Fetching a user
$user = $this->rm()->findOrFail(User::getResourceType(), $userId);
return $this->response()->item($user);

// After updating a user
$user->setName($request->input('data.attributes.name'));
$this->em()->flush();
return $this->response()->item($user);
```

**Example JSON Output (200 OK):**
```json
{
  "data": {
    "type": "users",
    "id": "123",
    "attributes": {
      "name": "Updated Name",
      "email": "user@example.com",
      "createdAt": "..."
    },
    "links": {
      "self": "/api/users/123"
    }
  }
}
```

### Resource Collection (`collection`)

Used when returning a list or collection of resources. Typically used for `List` operations. Handles pagination links automatically if pagination is applied (e.g., via `PaginatesResourceTrait`). Sets HTTP status `200 OK` by default.

```php
// Inside ListUsersAction's handle method

$page = $this->request()->page(); // Get pagination params
$usersQuery = $this->rm()->queryAll(User::getResourceType());

// Apply filtering, sorting, pagination...
// $users = $this->paginate($usersQuery, $page); // Example using trait

// Assume $users is now a Paginator instance or array/collection
return $this->response()->collection($users);
```

**Example JSON Output (200 OK):**
```json
{
  "meta": { // Example meta added by pagination
    "page": {
        "currentPage": 1,
        "perPage": 15,
        "total": 50,
        "lastPage": 4
    }
  },
  "data": [
    {
      "type": "users",
      "id": "1",
      "attributes": { ... },
      "links": { "self": "/api/users/1" }
    },
    {
      "type": "users",
      "id": "2",
      "attributes": { ... },
      "links": { "self": "/api/users/2" }
    }
    // ... more users
  ],
  "links": { // Example links added by pagination
    "first": "/api/users?page[number]=1",
    "last": "/api/users?page[number]=4",
    "prev": null,
    "next": "/api/users?page[number]=2"
  }
}
```

### Successful Creation (`created`)

Specifically for responding to successful resource creation (`POST` requests). It automatically sets the HTTP status to `201 Created` and includes a `Location` header pointing to the newly created resource.

```php
// Inside CreateUserAction's handle method

$newUser = new User();
// ... set attributes from $request->validated() ...
$this->em()->persist($newUser);
$this->em()->flush();

return $this->response()->created($newUser);
```

**Example JSON Output (201 Created):**
(Response body is identical to `item()`, but status code and Location header differ)

### Successful Deletion (`noContent`)

Used for successful `DELETE` requests where no response body is needed. Sets HTTP status `204 No Content`.

```php
// Inside RemoveUserAction's handle method

$user = $this->rm()->findOrFail(User::getResourceType(), $userId);
$this->em()->remove($user);
$this->em()->flush();

return $this->response()->noContent();
```

**Example Response:**
-   Status Code: `204 No Content`
-   Response Body: *(Empty)*

### Meta-Only Responses (`meta`)

If you need to return a response containing only top-level `meta` information without any `data`.

```php
// Example: Returning API health status
$metadata = [
    'status' => 'operational',
    'timestamp' => now()->toIso8601String(),
];
return $this->response()->meta($metadata);
```

**Example JSON Output (200 OK):**
```json
{
  "meta": {
     "status": "operational",
     "timestamp": "..."
  }
}
```

### Error Responses

While you *can* manually create error responses using the `ResponseFactory`, it's generally **not necessary**. The package's exception handler (`Sowl\JsonApi\Exceptions\Handler`) automatically catches relevant exceptions (like `NotFoundHttpException`, `ValidationException`, `Sowl\JsonApi\Exceptions\JsonApiException`, `Symfony\Component\HttpKernel\Exception\HttpException`) and formats them into standard JSON:API error objects.

You should typically throw appropriate exceptions from your Actions or Form Requests and let the handler deal with formatting the error response.

## How Transformers are Used

When you pass an entity or collection to methods like `item()`, `collection()`, or `created()`, the `ResponseFactory` internally does the following:

1.  Determines the resource type (e.g., "users").
2.  Finds the corresponding Transformer registered for that type (e.g., `UserTransformer`).
3.  Uses the Transformer's `transform()` method to get the `attributes`.
4.  Handles includes (`?include=...`) by calling the Transformer's `includeXyz()` methods.
5.  Assembles the final JSON:API document structure (`data`, `included`, `links`).

## Best Practices

-   **Use Factory Methods:** Always use the `ResponseFactory` methods (`item`, `collection`, `created`, `noContent`) instead of manually building response arrays. This ensures spec compliance.
-   **Let Exceptions Handle Errors:** Throw standard HTTP exceptions, `JsonApiException`, or `ValidationException` and allow the package's exception handler to format JSON:API error responses. Avoid manual error formatting unless absolutely necessary.
-   **Transformers Shape the Data:** Remember that the *content* of the `data` object is determined by your Transformers. The `ResponseFactory` orchestrates the process.
