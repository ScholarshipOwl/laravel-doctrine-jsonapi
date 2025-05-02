# Relationships

Relationships define how resources are connected to each other in your API. JSON:API supports several types of relationships, such as one-to-one, one-to-many, and many-to-many.

## Key Concepts
- **Relationship Types:**
  - To-One: A resource is linked to a single other resource.
  - To-Many: A resource is linked to multiple resources.
- **Relationship Endpoints:** The package automatically generates endpoints for managing relationships if configured.
- **Relationship Data:** Relationships are represented using `relationships` objects in JSON:API responses.

## Example
```json
{
  "type": "posts",
  "id": "1",
  "attributes": {
    "title": "Hello World"
  },
  "relationships": {
    "author": {
      "data": { "type": "users", "id": "1" }
    },
    "comments": {
      "data": [
        { "type": "comments", "id": "5" },
        { "type": "comments", "id": "7" }
      ]
    }
  }
}
```

## Best Practices
- Clearly define all relationships in your entity and transformer.
- Use the package’s helpers to manage relationship endpoints and data.
- Avoid circular or deeply nested relationships unless necessary.