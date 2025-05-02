# ResourceInterface

**Location:** `src/ResourceInterface.php`

The `ResourceInterface` defines the contract that every Doctrine entity must implement to be exposed as a JSON:API resource.

## Responsibilities
- Ensures entities can be uniquely identified and transformed for API responses.
- Used by the ResourceManager and transformers to standardize resource handling.

## Key Methods
- `getId()`: Returns the unique identifier for the resource.
- `getResourceType()`: Returns the JSON:API resource type as a string.
- `transformer()`: Returns the transformer instance for this resource.

## Example Implementation
```php
class User implements ResourceInterface {
    public function getId() { return $this->id; }
    public function getResourceType() { return 'users'; }
    public function transformer() { return new UserTransformer(); }
}
```

## Extension Points
- Implement this interface in each entity you want to expose via JSON:API.
