# Meta Query Parameter Documentation

The `meta` query parameter is used to include additional metadata in the JSON:API responses. This can be useful for providing clients with extra information that is not part of the primary resource representation.

## Usage

To include metadata in your response, specify the `meta` query parameter in your request. The server will then return a `meta` object in the response that contains the requested information. The `meta` parameter follows the JSON:API sparse fieldsets format, where you specify the resource type as the key and the requested meta fields as the value.

### Example

```http
GET /api/resources?meta[users]=totalCount
```

In this example, the request asks for the `totalCount` meta field for the `users` resource type to be included in the response.

### Response

```json
{
  "data": {
    "id": "8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b",
    "type": "users",
    "attributes": {
      "email": "test1email@test.com",
      "name": "testing user1"
    },
    "meta": {
      "totalCount": 42
    }
  }
}
```

## Meta with Included Relationships

You can request meta fields for included relationships using the `include` parameter along with the `meta` parameter. This allows you to get metadata for both the primary resource and its included relationships in a single request.

### Example with Included Relationships

```http
GET /api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b?include=roles&meta[users]=lastLogin&meta[roles]=memberCount
```

In this example, the request asks for:
- The `roles` relationship to be included
- The `lastLogin` meta field for the `users` resource
- The `memberCount` meta field for the included `roles` resources

### Response with Included Relationships

```json
{
  "data": {
    "id": "8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b",
    "type": "users",
    "attributes": {
      "email": "test1email@test.com",
      "name": "testing user1"
    },
    "relationships": {
      "roles": {
        "data": [
          {
            "id": "2",
            "type": "roles"
          }
        ],
        "links": {
          "self": "/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles",
          "related": "/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles"
        }
      }
    },
    "meta": {
      "lastLogin": "2023-06-15T10:30:00Z"
    }
  },
  "included": [
    {
      "id": "2",
      "type": "roles",
      "attributes": {
        "name": "admin"
      },
      "meta": {
        "memberCount": 5
      }
    }
  ]
}
```

## Meta with Relationships

You can also request meta fields for relationships. This is useful when you need additional information about related resources.

### Example with Relationships

```http
GET /api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles?meta[roles]=count,lastUpdated
```

In this example, the request asks for the `count` and `lastUpdated` meta fields for the `roles` relationship of a specific user.

### Response with Relationships

```json
{
  "data": [
    {
      "id": "2",
      "type": "roles",
      "attributes": {
        "name": "admin"
      },
      "meta": {
        "count": 5,
        "lastUpdated": "2023-06-15T14:30:00Z"
      }
    }
  ],
  "links": {
    "self": "/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles",
    "related": "/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles"
  }
}
```

### Example with Relationship Endpoints

You can also request meta fields when accessing relationship endpoints:

```http
GET /api/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles?meta[roles]=totalCount
```

Response:

```json
{
  "data": [
    {
      "id": "2",
      "type": "roles"
    }
  ],
  "meta": {
    "totalCount": 1
  },
  "links": {
    "self": "/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/relationships/roles",
    "related": "/users/8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b/roles"
  }
}
```

## Implementation

When a request includes the `meta` parameter, the system:

1. Parses the requested meta fields from the request using the format `meta[resourceType]=field1,field2`
2. Checks if the requested meta fields are available in the transformer
3. Calls the corresponding meta methods on the transformer (e.g., `metaTotalCount()` for the `totalCount` field)
4. Includes the results in the response's `meta` object for that resource

## Available Meta Fields
- `totalCount`: The total number of resources available.
- Additional fields may be defined based on specific resource types or use cases.

## Adding Custom Meta Fields

To add a custom meta field to a resource, implement a method in your transformer class following this pattern:

```php
public function metaFieldName($resource)
{
    // Calculate and return the meta value
    return $value;
}
```

For example, to implement the `totalCount` meta field:

```php
public function metaTotalCount($resource)
{
    return $this->repository->count();
}
```

Don't forget to add the field name to the `$availableMetas` array in your transformer:

```php
protected array $availableMetas = ['totalCount'];
```

## Notes
- Ensure that the requested meta fields are supported by the API. If a requested field is not available, it will be omitted from the response.
- The `meta` object can include any relevant information that helps the client understand the context of the response.
- If a meta field is requested but no corresponding method exists in the transformer, a runtime exception will be thrown.
