# Transformers

Transformers are responsible for converting your Doctrine entities (resources) into JSON:API-compliant arrays for API responses. They define which attributes and relationships are exposed and how they are formatted.

## Key Concepts
- **Transformer Class:** Each resource should have a corresponding transformer class, usually extending `AbstractTransformer`.
- **Attributes:** Define which fields are included in the API response.
- **Relationships:** Specify how related resources are represented.

## Implementing a Transformer
```php
class UserTransformer extends AbstractTransformer {
    public function transform(User $user) {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            // Add more attributes as needed
        ];
    }
}
```

## Example Output
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
- Only expose attributes necessary for API consumers.
- Use transformers to control serialization and hide internal fields.
- Leverage relationships for nested or linked resources.