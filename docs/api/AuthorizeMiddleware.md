# Authorize Middleware

**Location:** `src/Default/Middleware/Authorize.php`

The `Authorize` middleware is responsible for enforcing authorization policies on JSON:API resource and relationship endpoints.

## Responsibilities
- Integrates with Laravel's Gate and your defined policies to check permissions for each request.
- Maps HTTP methods to abilities (e.g., GET to `view`, POST to `create`).
- Supports both resource and relationship authorization.

## Key Features
- Automatically applied to all JSON:API routes via the default middleware group.
- Uses the ResourceManager to resolve resources and policies.
- Can be customized or replaced if you need advanced authorization logic.

## Example Usage
You typically do not call this middleware directly. It is registered in the middleware group and applied to your routes automatically:

```php
'middleware' => [
    'jsonapi', // includes Authorize middleware
],
```

## Extension Points
- Customize your policies for each resource for fine-grained access control.
- Replace or extend the middleware if you need to change the authorization flow.
