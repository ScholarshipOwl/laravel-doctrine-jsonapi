# AbstractTransformer

**Location:** `src/AbstractTransformer.php`

The `AbstractTransformer` class is the base for all Fractal transformers used to serialize resources into JSON:API-compliant responses.

## Responsibilities
- Defines how entities are transformed into API responses.
- Implements core serialization logic and extension points for custom fields and relationships.

## Key Methods
- `create(...$args)`: Static factory for transformer instances.
- `getAvailableMetas()`: Returns available metadata fields.
- `item($resource)`: Serializes a single resource.
- `collection($resources)`: Serializes a collection of resources.

## Example Usage
```php
class UserTransformer extends AbstractTransformer {
    public function transform(User $user) {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
        ];
    }
}
```

## Extension Points
- Extend this class to define custom serialization logic for your entities.
