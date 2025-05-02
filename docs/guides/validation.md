# Validation in JSON:API

This guide explains how validation works in the Laravel Doctrine JSON:API package and how it integrates with Scribe documentation.

## Introduction

Validation is a crucial part of any API. In JSON:API, validation ensures that incoming requests conform to both the JSON:API specification and your application's business rules. This package leverages Laravel's validation system to validate incoming requests and generate appropriate documentation.

## Validation Rules

### Setting Up Validation Rules

Validation rules for your JSON:API endpoints are defined in your controller or request classes. The package uses these rules to:

1. Validate incoming requests
2. Generate documentation examples for request bodies
3. Document validation errors

Example of defining validation rules in a request class:

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'data.type' => 'required|string|in:users',
            'data.attributes.name' => 'required|string|max:255',
            'data.attributes.email' => 'required|email|unique:users,email',
            'data.attributes.password' => 'required|string|min:8',
            'data.relationships.roles.data' => 'sometimes|array',
            'data.relationships.roles.data.*.type' => 'required_with:data.relationships.roles.data|string|in:roles',
            'data.relationships.roles.data.*.id' => 'required_with:data.relationships.roles.data.*.type|string|exists:roles,id',
        ];
    }
}
```

### JSON:API Specific Validation

The package includes validation rules specific to JSON:API:

1. **Type validation**: Ensures the resource type matches the expected type
2. **ID validation**: Validates resource IDs
3. **Relationship validation**: Validates relationship structure and linkage
4. **Attribute validation**: Ensures attributes conform to expected formats

## Integration with Scribe

### How Validation Rules Generate Documentation

Scribe uses your validation rules to generate documentation in several ways:

1. **Request Body Examples**: Scribe analyzes your validation rules to generate example request bodies that would pass validation
2. **Required Fields**: Fields marked as required in your validation rules are clearly indicated in the documentation
3. **Field Types**: Validation rules like `integer`, `string`, `boolean` help Scribe determine the correct data types for fields
4. **Validation Constraints**: Rules like `max:255`, `email`, `in:option1,option2` are documented as constraints on the fields
5. **Error Responses**: Scribe generates example error responses based on validation failures

### Example Documentation Flow

When Scribe generates documentation for an endpoint:

1. It identifies the controller method and any associated form request classes
2. It extracts validation rules from these classes
3. It generates example request bodies that would pass validation
4. It documents the structure of validation error responses

## JSON:API Validation Errors

When validation fails, the package returns a JSON:API compliant error response:

```json
{
  "errors": [
    {
      "status": "422",
      "title": "Validation Error",
      "detail": "The name field is required.",
      "source": {
        "pointer": "/data/attributes/name"
      }
    },
    {
      "status": "422",
      "title": "Validation Error",
      "detail": "The email must be a valid email address.",
      "source": {
        "pointer": "/data/attributes/email"
      }
    }
  ]
}
```

## Best Practices

1. **Use Form Request Classes**: Separate validation logic into dedicated form request classes for cleaner code
2. **Follow JSON:API Paths**: Use dot notation to validate nested JSON:API structures (`data.attributes.name`)
3. **Document Custom Rules**: If you have custom validation rules, make sure to document them
4. **Include Examples**: Provide examples of valid request bodies in your controller method docblocks
5. **Validate Relationships**: Don't forget to validate relationship structures and linkage

## Example Validation Rules for Common Scenarios

### Creating a Resource

```php
[
    'data.type' => 'required|string|in:articles',
    'data.attributes.title' => 'required|string|max:255',
    'data.attributes.content' => 'required|string',
    'data.relationships.author.data.type' => 'required|string|in:users',
    'data.relationships.author.data.id' => 'required|string|exists:users,id',
]
```

### Updating a Resource

```php
[
    'data.id' => 'required|string|exists:articles,id',
    'data.type' => 'required|string|in:articles',
    'data.attributes.title' => 'sometimes|string|max:255',
    'data.attributes.content' => 'sometimes|string',
]
```

### Updating a Relationship

```php
[
    'data.*.type' => 'required|string|in:tags',
    'data.*.id' => 'required|string|exists:tags,id',
]
```

## Additional Resources

- [Laravel Validation Documentation](https://laravel.com/docs/validation)
- [JSON:API Specification - Errors](https://jsonapi.org/format/#errors)
- [Scribe Documentation on Body Parameters](https://scribe.knuckles.wtf/laravel/reference/bodyparameters)
