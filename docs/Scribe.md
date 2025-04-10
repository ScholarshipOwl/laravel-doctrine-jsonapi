# Laravel Doctrine JSON:API Documentation with Scribe

This package provides custom [Scribe](https://scribe.knuckles.wtf/laravel/) strategies to document your JSON:API endpoints automatically.

API documentation is an essential part of any API. It provides a clear understanding of the endpoints,
their parameters, expected responses and errors. With this documentation, developers can easily consume
the API, without having to contact the API developers directly. It also helps to
reduce the number of support requests, since the documentation answers many of the questions that
developers may have.

## Installation

1. Install Scribe in your Laravel application:

```bash
composer require knuckleswtf/scribe
```

2. Publish Scribe's configuration:

```bash
php artisan vendor:publish --tag=scribe-config
```

3. Publish the JSON:API language files for Scribe:

```bash
php artisan vendor:publish --tag=jsonapi-scribe-translations
```

This will publish the language files to your application's `lang/jsonapi` directory, which are used by the Scribe strategies to generate proper documentation text.

## Configuration

To use the JSON:API custom strategies, you need to add them to your Scribe configuration. In your `config/scribe.php` file:

1. Add our strategies to the corresponding sections:

```php
'strategies' => [
    'metadata' => [
        // ...existing strategies
        \Sowl\JsonApi\Scribe\Metadata\GetFromJsonApiRouteStrategy::class,
    ],
    'urlParameters' => [
        // ...existing strategies
        \Sowl\JsonApi\Scribe\UrlParameters\GetFromJsonApiRouteStrategy::class,
    ],
    'queryParameters' => [
        // ...existing strategies
        \Sowl\JsonApi\Scribe\QueryParameters\AddJsonApiQueryParametersStrategy::class,
    ],
    'headers' => [
        // ...existing strategies
        \Sowl\JsonApi\Scribe\Headers\AddJsonApiHeadersStrategy::class,
    ],
    'responses' => [
        // ...existing strategies
        \Sowl\JsonApi\Scribe\Responses\UseJsonApiResourceResponseStrategy::class,
    ],
],
```

2. Add the JSON:API OpenAPI spec generator:

```php
'openapi' => [
    // ...other api config
    'generators' => [
        \Sowl\JsonApi\Scribe\JsonApiSpecGenerator::class,
    ],
],
```

3. Configure the custom route matcher:

```php
'routeMatcher' => \Sowl\JsonApi\Scribe\RouteMatcher::class,
```

This configuration enables:
- Proper handling of JSON:API routes with dynamic resource types and relationships
- Enhanced OpenAPI spec generation with JSON:API specific parameter styles
- Correct documentation of deep object parameters like fields, meta, filter, and page

## Generation

After configuring Scribe with our strategies, generate the documentation:

```bash
php artisan scribe:generate
```

The generated documentation will include:

- JSON:API compliant request and response formats
- All available query parameters for filtering, sorting, and pagination
- Proper headers for JSON:API compatibility
- Relationship handling examples

### Example Flow

When documenting an endpoint:

1. The strategy identifies the resource type and action (create, update, fetch, etc.)
2. For request bodies, validation rules are extracted to determine the structure
3. For responses, entity factories create sample entities
4. Transformers convert the entities to JSON:API format
5. The documentation shows both valid request examples and expected responses

This integration with factories and validation rules ensures that your API documentation accurately reflects your application's data model and validation constraints.

## Strategies Overview

### GetFromJsonApiRouteStrategy (Metadata)
[Source: [`src/Scribe/Metadata/GetFromJsonApiRouteStrategy.php`](../src/Scribe/Metadata/GetFromJsonApiRouteStrategy.php)]

This strategy extracts metadata from JSON:API routes to provide meaningful documentation:
- Automatically generates titles based on resource types and actions
- Groups related endpoints together by resource type
- Handles both standard and relationship endpoints
- Supports dynamic resource type routes

### GetFromJsonApiRouteStrategy (URL Parameters)
[Source: [`src/Scribe/UrlParameters/GetFromJsonApiRouteStrategy.php`](../src/Scribe/UrlParameters/GetFromJsonApiRouteStrategy.php)]

Documents URL parameters specific to JSON:API routes:
- `{id}`: Resource identifier
- Provides descriptions and validation rules for each parameter

### AddJsonApiQueryParametersStrategy
[Source: [`src/Scribe/QueryParameters/AddJsonApiQueryParametersStrategy.php`](../src/Scribe/QueryParameters/AddJsonApiQueryParametersStrategy.php)]

Adds standard JSON:API query parameters for GET endpoints:
- `filter[field]`: Filter resources by field values
- `sort`: Sort resources by fields (prefix with `-` for descending)
- `page[number]`: Page number for pagination
- `page[size]`: Number of items per page
- `include`: Include related resources (comma-separated)
- `fields[type]`: Sparse fieldsets to select specific fields

### AddJsonApiHeadersStrategy
[Source: [`src/Scribe/Headers/AddJsonApiHeadersStrategy.php`](../src/Scribe/Headers/AddJsonApiHeadersStrategy.php)]

Adds required JSON:API headers to all endpoints:
- `Accept: application/vnd.api+json`: Required for all requests
- `Content-Type: application/vnd.api+json`: Required for POST/PATCH/DELETE requests

### UseJsonApiResourceResponseStrategy
[Source: [`src/Scribe/Responses/UseJsonApiResourceResponseStrategy.php`](../src/Scribe/Responses/UseJsonApiResourceResponseStrategy.php)]

Generates JSON:API compliant response examples:
- Shows proper response structure with data, included, and meta sections
- Includes relationship links and resource linkage
- Documents error responses in JSON:API format
- Provides examples for different HTTP status codes

## Example Usage

Here's how the strategies work together to document a typical JSON:API endpoint:

```php
// Route definition
Route::get('/{resourceType}/{id}/relationships/{relationship}', 'JsonApiController@showRelationship');

// Generated documentation will include:
// - URL parameters: resourceType, id, relationship
// - Query parameters: include, fields, etc.
// - Headers: Accept, Content-Type
// - Response format: JSON:API compliant structure
// - Proper grouping with related endpoints
```

When processing these routes, the strategies will:

1. Convert dynamic placeholders like `{resourceType}` and `{relationship}` into actual registered resources and relationships
2. Generate documentation for each registered resource type (e.g., "users", "articles", "comments")
3. Include real relationship examples based on the entity relationships defined in your application
4. Group endpoints by resource type, making the documentation more organized and intuitive
5. Use proper JSON:API structure in request and response examples

For example, a route like `/{resourceType}/{id}` might be documented as multiple concrete endpoints:
- `/users/{id}` - Get a specific user
- `/articles/{id}` - Get a specific article
- `/comments/{id}` - Get a specific comment

Similarly, relationship endpoints will be documented with actual relationship names from your entities.

## Response Documentation

The package generates realistic JSON:API compliant response examples for your API documentation using a combination of entity factories and transformers:

```php
// Entity creation using Laravel Doctrine's factory system
$entity = $this->factory()->of($resourceClass)->create();

// Transformation to JSON:API format
$transformer = $this->getTransformerForResource($resourceType);
$response = $this->response()->item($entity, $transformer);
```

This process ensures:
- Response examples contain realistic data that matches your entity structure
- Relationships between entities are properly represented
- All required fields are populated with sensible values
- Proper JSON:API structure with resource identifiers, attributes, and relationships
- Validation error responses with appropriate error codes and messages
- Pagination metadata for collection responses

For detailed instructions on creating entity factories, see our [Factories Documentation](./Factories.md). For more information about transformers, see [Scribe's transformer documentation](https://scribe.knuckles.wtf/laravel/documenting/responses#transformers).

### Database Configuration for Documentation Generation

When generating documentation, Scribe needs to create entity instances to produce realistic response examples. For Laravel Doctrine JSON:API, this process is adapted to work with Doctrine entities instead of Eloquent models.

#### How Entity Instances Are Generated

The `UseJsonApiResourceResponseStrategy` generates entity instances using Laravel Doctrine's factory system:

1. First, it attempts to create an entity using the factory: `$this->factory()->of($resourceClass)->create()`
2. The entity is then passed to the appropriate transformer to generate a JSON:API response
3. All database operations are wrapped in transactions to prevent permanent changes to your database

For more information about how Scribe generates model instances, see the [official Scribe documentation](https://scribe.knuckles.wtf/laravel/documenting/responses#how-model-instances-are-generated).

#### Using SQLite In-Memory Database

For documentation generation, we recommend using an SQLite in-memory database to:
- Speed up the documentation generation process
- Avoid affecting your development or production database
- Ensure consistent documentation regardless of existing data

Configure your database connection in `config/database.php`:

```php
'sqlite' => [
    'driver' => 'sqlite',
    'database' => ':memory:',
    'prefix' => '',
],
```

Then set this connection as the default in your `.env.docs` file or when running the documentation generation command:

```bash
DB_CONNECTION=sqlite php artisan scribe:generate
```

#### Database Transactions

Scribe automatically wraps all database operations in transactions. You can configure which connections should be transacted in your `config/scribe.php` file:

```php
'database_connections_to_transact' => [config('database.default')],
```

This ensures that any entities created during documentation generation are rolled back after the process completes.

### Example Response Documentation

```json
{
  "data": {
    "type": "articles",
    "id": "1",
    "attributes": {
      "title": "JSON:API paints my bikeshed!",
      "content": "The shortest article. Ever."
    },
    "relationships": {
      "author": {
        "links": {
          "self": "/articles/1/relationships/author",
          "related": "/articles/1/author"
        },
        "data": { "type": "people", "id": "9" }
      }
    }
  }
}
```

## Body Parameters Documentation

The package uses Laravel's validation rules to generate appropriate request body examples for your JSON:API endpoints.

### Validation Rules for Body Parameters

Body parameters are documented based on the validation rules defined in your application:

1. For POST/PATCH endpoints, validation rules determine required and optional fields
2. Field types (string, integer, boolean, etc.) are inferred from validation rules
3. Format constraints (email, date, URL, etc.) are respected in the examples
4. Relationship validation rules are used to structure relationship data correctly

For detailed information about validation rules, see our [Validation Documentation](./Validation.md) and [Scribe's documentation on validation rules](https://scribe.knuckles.wtf/laravel/documenting/query-body-parameters#validation-rules).

### Example Body Parameter Documentation

```json
{
  "data": {
    "type": "articles",
    "attributes": {
      "title": "JSON:API paints my bikeshed!",
      "content": "The shortest article. Ever."
    },
    "relationships": {
      "author": {
        "data": { "type": "people", "id": "9" }
      }
    }
  }
}
```

## Additional Resources

- [JSON:API Specification](https://jsonapi.org)
- [Scribe Documentation](https://scribe.knuckles.wtf/laravel/)
