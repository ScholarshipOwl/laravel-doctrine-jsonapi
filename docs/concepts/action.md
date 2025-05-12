# Actions: Handling API Requests

Actions are the workhorses of your API within the Laravel Doctrine JSON:API package. Think of them as specialized controllers designed to handle specific JSON:API operations, such as retrieving a list of resources, creating a new one, or managing relationships. Each action encapsulates the logic for a single, well-defined task.

They are responsible for:

-   Interpreting the incoming HTTP request.
-   Executing the core business logic using validated request data.
-   Constructing and returning JSON:API-compliant responses using the `ResponseFactory`.

Using Actions promotes a clean separation of concerns, isolating the logic for each API endpoint and making your application easier to understand, test, and maintain.

**On this page:**

-   [Core Concepts](#core-concepts)
-   [Standard Actions Provided](#standard-actions-provided)
-   [Creating Custom Actions](#creating-custom-actions)
    -   [When to Create Custom Actions](#when-to-create-custom-actions)
    -   [Example: ActivateUserAction](#example-activateuseraction)
-   [Leveraging Helper Traits](#leveraging-helper-traits)
-   [Best Practices](#best-practices)

---

## Core Concepts

-   **Base Class:** Actions typically extend `Sowl\JsonApi\Action\AbstractAction`. This base class provides convenient access to essential services like the incoming `Request`, the Doctrine `EntityManager`, the `ResourceManager`, and the `ResponseFactory`.
-   **Single Responsibility:** Each Action class should handle one specific API operation (e.g., showing *one* user, listing *all* users, adding *one* role to a user).
-   **Routing:** Actions are usually mapped directly to routes in your `routes/api.php` file.

## Standard Actions Provided

This package comes with a set of pre-built Actions for common JSON:API operations, covering both primary resources and relationships. These serve as excellent examples and can often be used directly or extended.

-   **Resource Actions (`src/Action/Resource`):**
    *   `ListResourcesAction`: Handles fetching a collection of resources (e.g., `GET /api/users`).
    *   `ShowResourceAction`: Handles fetching a single resource by ID (e.g., `GET /api/users/{id}`).
    *   `CreateResourceAction`: Handles creating a new resource (e.g., `POST /api/users`).
    *   `UpdateResourceAction`: Handles updating an existing resource (e.g., `PATCH /api/users/{id}`).
    *   `RemoveResourceAction`: Handles deleting a resource (e.g., `DELETE /api/users/{id}`).
-   **Relationship Actions (`src/Action/Relationships`):**
    *   Actions for viewing relationship data (e.g., `GET /api/users/{id}/relationships/roles`).
    *   Actions for viewing related resources (e.g., `GET /api/users/{id}/roles`).
    *   Actions for adding to, replacing, or removing from relationships (e.g., `POST`, `PATCH`, `DELETE` on relationship URLs).

**We strongly recommend browsing the code in the `src/Action/Resource` and `src/Action/Relationships` directories** to understand how these standard operations are implemented. They demonstrate best practices for request handling, data fetching, validation, and response generation within the package's framework. Use them as a reference when building your own actions.

## Creating Custom Actions

While the standard actions cover many use cases, you'll often need custom actions for specific business logic or non-standard operations.

### When to Create Custom Actions

-   Implementing endpoints that don't map directly to simple CRUD operations (e.g., `/api/users/{id}/activate`, `/api/orders/process-batch`).
-   Performing complex validation or business logic before or after the primary operation.
-   Handling actions that affect multiple resources at once.
-   Integrating with external services as part of an action.

### Example: ActivateUserAction

Imagine you need a dedicated endpoint to activate a user account, which isn't a standard CRUD operation.

```php
<?php

namespace App\Http\Actions\Users; // Example namespace

use App\Entities\User; // Assuming User entity
use Sowl\JsonApi\Action\AbstractAction;
use Sowl\JsonApi\Response\Response;
use Sowl\JsonApi\Exceptions\JsonApiException; // For standard error responses

class ActivateUserAction extends AbstractAction
{
    /**
     * Handle the activation request.
     * This example assumes the user ID is passed via a route parameter.
     */
    public function handle(): Response
    {
        /** @var User $user */
        $user = $this->request()->resource();

        // --- Your Business Logic ---
        if ($user->isActive()) {
             // You might throw a Conflict error or simply return the current state
             // throw new JsonApiException('User is already active.', 409); // Conflict
             return $this->response()->item($user); // Return current state (already active)
        }

        $user->activate(); // Assume an 'activate' method exists on your User entity
        // --- End Business Logic ---

        $this->em()->flush(); // Persist the changes to the database

        // Return the updated user resource using the response factory
        return $this->response()->item($user);
    }
}

```
*Note: The exact way you retrieve the resource (e.g., `rm()->findOrFail()`, `request()->resource()` if using route model binding) depends on your route definition and application setup.*

## Leveraging Helper Traits

The package provides several traits within the `src/Action` namespace designed to be used within your Action classes to reduce boilerplate code:

-   `CalculatesChangeSetTrait`: Useful for determining what changed during an update operation, often used within `UpdateResourceAction`.
-   `FiltersResourceTrait`: Helps implement resource filtering based on `?filter[...]` query parameters. Used heavily in `ListResourcesAction`.
-   `PaginatesResourceTrait`: Implements pagination logic based on `?page[...]` query parameters. Used in `ListResourcesAction`.
-   `ValidatesResourceTrait`: Provides helpers for validating incoming request data, typically integrating with Laravel Form Requests. Used in `CreateResourceAction` and `UpdateResourceAction`.

You can `use` these traits in your custom actions (or actions extending the base ones) to easily add standard filtering, pagination, and validation capabilities.

## Best Practices

-   **Single Responsibility:** Keep each action focused on *one* specific task or API operation. Don't overload actions with unrelated logic.
-   **Leverage Base Actions:** Extend the standard actions (`ListResourcesAction`, `CreateResourceAction`, etc.) from `src/Action/Resource` and `src/Action/Relationships` whenever your needs align closely with standard CRUD or relationship management. Override methods as needed.
-   **Use Traits:** Incorporate the provided helper traits (`FiltersResourceTrait`, `PaginatesResourceTrait`, `ValidatesResourceTrait`) for common functionalities.
-   **Dependency Injection:** Use constructor injection for any additional services your action requires beyond those provided by `AbstractAction`.
-   **Consult Existing Code:** When in doubt, refer to the implementations in `src/Action/Resource` and `src/Action/Relationships`. They are the best reference for how to structure your actions within this package.
