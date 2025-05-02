# Response

Responses in Laravel Doctrine JSON:API are formatted according to the JSON:API specification. The package provides helpers and conventions to ensure your API responses are consistent and standards-compliant.

## Key Concepts
- **Response Factory:** Use the response factory or helper methods to generate JSON:API responses.
- **Standard Structure:** Responses include `data`, `attributes`, `relationships`, and optional `meta` and `links`.
- **Error Handling:** Errors are returned in a standardized JSON:API error object format.

## Example Success Response
```json
{
  "data": {
    "type": "users",
    "id": "1",
    "attributes": {
      "name": "John Doe"
    }
  }
}
```

## Example Error Response
```json
{
  "errors": [
    {
      "status": "422",
      "title": "Validation Error",
      "detail": "The name field is required."
    }
  ]
}
```

## Best Practices
- Always return responses using the package’s helpers for consistency.
- Include relevant meta and links objects where appropriate.
- Handle errors using the JSON:API error format for clear client feedback.