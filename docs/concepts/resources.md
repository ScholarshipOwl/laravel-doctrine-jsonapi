# Resources

Resources are the primary building blocks of your JSON:API. In Laravel Doctrine JSON:API, a resource typically corresponds to a Doctrine entity that implements the `ResourceInterface`. Each resource represents a type of data (such as users, posts, or comments) that your API exposes.

## Key Concepts
- **Resource Type:** The JSON:API `type` for the entity (e.g., `users`, `posts`).
- **Resource Identifier:** Each resource must have a unique identifier (`id`).
- **Resource Registration:** Resources must be registered in your `jsonapi.php` config file so the package can manage and expose them.

## Implementing a Resource
To make an entity a JSON:API resource, implement the `ResourceInterface`:
```php
class User implements ResourceInterface {
    public function getId() { return $this->id; }
    public function getResourceType() { return 'users'; }
    public function transformer() { return new UserTransformer(); }
}
```

## Example
```json
{
  "type": "users",
  "id": "1",
  "attributes": {
    "name": "John Doe"
  }
}
```

## Best Practices
- Use plural, lowercase names for resource types.
- Keep resource attributes limited to relevant fields.
- Register all resources in your configuration for auto-discovery.