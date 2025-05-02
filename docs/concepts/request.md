# Request

The package provides tools and conventions for handling JSON:API-compliant requests. This includes parsing incoming data, validating payloads, and making resource data available to your actions and controllers.

## Key Concepts
- **Request Classes:** Extend Laravel’s request classes to handle JSON:API payloads.
- **Validation:** Use built-in traits and helpers to validate data according to JSON:API structure.
- **Resource Extraction:** Easily extract resource data, attributes, and relationships from the request.

## Example: Creating a Resource
```json
{
  "data": {
    "type": "users",
    "attributes": {
      "name": "John Doe"
    }
  }
}
```

## Best Practices
- Always validate the structure and content of incoming requests.
- Use the provided request classes and traits to reduce boilerplate.
- Handle errors and invalid data gracefully, returning JSON:API error objects.