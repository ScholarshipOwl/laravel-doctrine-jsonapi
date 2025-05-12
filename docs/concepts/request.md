# Handling JSON:API Requests

Effectively handling incoming requests is crucial for a robust API. This package integrates seamlessly with **Laravel's Form Request** system, which is the recommended approach for validating incoming JSON:API payloads and authorizing actions *before* your core business logic is executed.

This ensures that by the time the request reaches your Action or Controller, you can be confident that the data is structured correctly according to the JSON:API specification and meets your application's validation rules.

**On this page:**

-   [Core Concepts](#core-concepts)
    -   [Content Negotiation](#content-negotiation)
    -   [The Role of Form Requests](#the-role-of-form-requests)
    -   [Expected Payload Structure](#expected-payload-structure)
-   [Validation with Form Requests](#validation-with-form-requests)
    -   [Example: CreateUserRequest](#example-createuserrequest)
    -   [Example: UpdateUserRequest](#example-updateuserrequest)
    -   [Automatic Error Formatting](#automatic-error-formatting)
-   [Accessing Validated Data](#accessing-validated-data)
-   [Best Practices](#best-practices)

## The Role of Form Requests

[Laravel's Form Requests](https://laravel.com/docs/validation#form-request-validation) are the ideal place to handle both **authorization** (can the current user perform this action?) and **validation** (does the incoming data meet the required criteria?).

You create a dedicated Request class (e.g., `App\Http\Requests\CreateUserRequest`) for each relevant endpoint. Laravel automatically resolves and runs this request class before your Action's `handle` method or Controller method is called. If authorization fails or validation rules are not met, Laravel automatically stops execution and sends back an appropriate JSON:API-formatted error response (handled by this package's exception handler).

### Expected Payload Structure

JSON:API defines a specific structure for request payloads, primarily within the top-level `data` object:

-   **Creating Resources (`POST /resource`)**:
    ```json
    {
      "data": {
        "type": "required-resource-type", // e.g., "users"
        "attributes": {
          "field1": "value1",
          "field2": "value2"
        },
        "relationships": {
          "toOneRel": {
            "data": { "type": "related-type", "id": "related-id" }
          },
          "toManyRel": {
            "data": [
              { "type": "related-type", "id": "id1" },
              { "type": "related-type", "id": "id2" }
            ]
          }
        }
      }
    }
    ```
-   **Updating Resources (`PATCH /resource/{id}`)**:
    ```json
    {
      "data": {
        "type": "required-resource-type", // Must match the endpoint's resource
        "id": "required-resource-id",   // Must match the {id} in the URL
        "attributes": {
          "fieldToUpdate": "new value"
          // Only include attributes being changed
        },
        "relationships": {
          // Include relationships being changed
        }
      }
    }
    ```

## Validation with Form Requests

Use Laravel's validation rules with dot notation to target specific parts of the JSON:API payload.

### Example: CreateUserRequest

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Entities\User; // Assuming User entity with getResourceType()
use App\Entities\Role; // Assuming Role entity
use Sowl\JsonApi\Rules\ResourceIdentifierRule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Example: Check if the authenticated user can create users
        // return $this->user()->can('create', User::class);
        return true; // Adjust authorization logic as needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // Validate top-level structure
            'data' => ['required', 'array'],
            'data.type' => ['required', 'string', Rule::in([User::getResourceType()])], // Ensure correct type

            // Validate attributes
            'data.attributes' => ['required', 'array'],
            'data.attributes.name' => ['required', 'string', 'max:255'],
            'data.attributes.email' => ['required', 'string', 'email', 'max:255', 'unique:App\Entities\User,email'], // Example unique rule
            'data.attributes.password' => ['required', 'string', 'min:8', 'confirmed'], // Example password validation

            // Use ResourceIdentifierRule for each item in the to-many relationship data array
            'data.relationships.roles.data.*' => [
                'required_with:data.relationships.roles.data',
                new ResourceIdentifierRule(Role::class)
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'data.type.in' => 'The :attribute must be \'' . User::getResourceType() . '\'.',
            'data.relationships.roles.data.*' => 'Related roles must be valid identifiers.',
        ];
    }
}
```

### Example: UpdateUserRequest

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Entities\User;
use App\Entities\Role;
use Sowl\JsonApi\Rules\ResourceIdentifierRule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Example: Check if the user can update the target user
        // $targetUser = $this->route('userId'); // Assuming route parameter 'userId'
        // return $this->user()->can('update', [User::class, $targetUser]);
        return true;
    }

    public function rules(): array
    {
        // Get the user ID from the route to ensure it matches the payload ID
        $userId = $this->route('userId'); // Adjust route parameter name if needed

        return [
            'data' => ['required', 'array'],
            'data.type' => ['required', 'string', Rule::in([User::getResourceType()])],
            'data.id' => ['required', 'string', Rule::in([$userId])], // Ensure payload ID matches URL ID

            // Validate attributes (make them optional for PATCH)
            'data.attributes' => ['sometimes', 'array'],
            'data.attributes.name' => ['sometimes', 'required', 'string', 'max:255'], // 'required' if present
            // Ensure email is unique, ignoring the current user
            'data.attributes.email' => ['sometimes', 'required', 'string', 'email', 'max:255', Rule::unique(User::class, 'email')->ignore($userId)],
            'data.attributes.password' => ['sometimes', 'required', 'string', 'min:8', 'confirmed'],

            // Use ResourceIdentifierRule for each item in the to-many relationship data array
            'data.relationships.roles.data.*' => [
                'required_with:data.relationships.roles.data',
                new ResourceIdentifierRule(Role::class)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'data.type.in' => 'The :attribute must be \'' . User::getResourceType() . '\'.',
            'data.id.in' => 'The payload ID must match the resource ID in the URL.',
            'data.relationships.roles.data.*' => 'Related roles must be valid identifiers.',
        ];
    }
}

```

### Automatic Error Formatting

When validation fails within a Form Request, Laravel's default behavior is overridden by this package's exception handler. It catches the `ValidationException` and automatically transforms it into a standard JSON:API error response (HTTP status 422 Unprocessable Entity), detailing the validation failures according to the specification. You generally don't need to write custom error handling code for validation failures.

## Accessing Validated Data

Once a Form Request passes validation, you can access the validated data within your Action or Controller method using `$request->validated()`:

```php
// Inside your Action's handle method or Controller method
// Assuming CreateUserRequest $request is injected

public function handle(CreateUserRequest $request): Response
{
    $validatedData = $request->validated();

    // Access specific parts using dot notation on the validated array
    $attributes = data_get($validatedData, 'data.attributes', []);
    $relationships = data_get($validatedData, 'data.relationships', []);

    // Now use $attributes and $relationships to create/update your entity...
    // Example:
    // $newUser = new User();
    // $newUser->setName($attributes['name']);
    // $newUser->setEmail($attributes['email']);
    // ... set password ...

    // ... handle relationships ...

    // $this->em()->persist($newUser);
    // $this->em()->flush();

    // return $this->response()->created($newUser);
}
```

## Best Practices

-   **Use Form Requests:** Always use Laravel Form Requests for validation and authorization of JSON:API endpoints.
-   **Validate Everything:** Define rules for all expected `attributes` and `relationships`. Be explicit about what is required and optional.
-   **Separate Concerns:** Keep validation logic within Form Requests, separate from your Action/Controller logic.
-   **Leverage Laravel Rules:** Use Laravel's extensive set of built-in validation rules and helpers like `Rule::in`, `Rule::exists`, `Rule::unique`.
-   **Authorization:** Implement proper authorization checks within the `authorize()` method of your Form Requests.