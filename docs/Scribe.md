# Documentation with Scribe

API documentation is an essential part of any API. It provides a clear understanding of the endpoints,
their parameters, expected responses and errors. With this documentation, developers can easily consume
the API, without having to contact the API developers directly. It also helps to
reduce the number of support requests, since the documentation answers many of the questions that
developers may have.

This package provides custom [Scribe](https://scribe.knuckles.wtf/laravel/) strategies to document your JSON:API endpoints automatically.

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

### API-only Docs

In your `config/scribe.php`, ensure API routes are documented:

```php
'routes' => [
    [
        'match' => [
            'prefixes' => ['*'],
            'domains' => ['*'],
        ],
        'exclude' => [
            'GET /', // Exclude root if present
            'GET /up', // Exclude health check route
            'GET /storage', // Exclude storage route
        ],
    ],
],
```

### Docs Type and Theme

For this skeleton, we recommend and use the `external_laravel` type, which serves the OpenAPI spec with an advanced external UI (Scalar):

```php
'type' => 'external_laravel',
'theme' => 'scalar',
```

This provides a modern, feature-rich OpenAPI UI for your documentation at `/docs` (or the configured docs URL).

### Restricting Access to Docs

You can restrict access to the API docs by adding middleware (e.g., `['auth']`) in the `laravel.middleware` config section:

```php
'laravel' => [
    ...
    'middleware' => ['auth'], // Only authenticated users can view docs
],
```

You may also use authorization middleware for fine-grained access control.

### Add JSON:API Scribe Strategies

In `config/scribe.php`, add the following to the `strategies` section to enable advanced JSON:API documentation:

```php
'strategies' => [
    'metadata' => [
        ...Defaults::METADATA_STRATEGIES,
        \Sowl\JsonApi\Scribe\Strategies\Metadata\GetFromResourceMetadataAttribute::class,
    ],
    'headers' => [
        ...Defaults::HEADERS_STRATEGIES,
        \Sowl\JsonApi\Scribe\Strategies\Headers\GetFromResourceAttributes::class,
    ],
    'urlParameters' => [
        ...Defaults::URL_PARAMETERS_STRATEGIES,
        \Sowl\JsonApi\Scribe\Strategies\UrlParameters\GetFromResourceRequestAttributes::class,
    ],
    'queryParameters' => [
        ...Defaults::QUERY_PARAMETERS_STRATEGIES,
        \Sowl\JsonApi\Scribe\Strategies\QueryParameters\GetFromResourceRequestAttributes::class,
    ],
    'bodyParameters' => [
        ...Defaults::BODY_PARAMETERS_STRATEGIES,
    ],
    'responses' => configureStrategy(
        [
            ...Defaults::RESPONSES_STRATEGIES,
            JsonApiStrategies\Responses\GetFromResourceResponseAttributes::class,
        ],
        Strategies\Responses\ResponseCalls::withSettings(
            only: ['GET *'],
            // Recommended: disable debug mode in response calls to avoid error stack traces in responses
            config: [
                'app.debug' => false,
            ]
        ),
    ),
    'responseFields' => [
        ...Defaults::RESPONSE_FIELDS_STRATEGIES,
    ]
],
```

**Important:**
Keep the response call strategy (as above) to allow Scribe to generate real example responses. The Sowl strategy should be appended after it for enhanced JSON:API support.

### Add the JSON:API OpenAPI Spec Generator

Add spec generator as a plugin to Scribe:

```php
'openapi' => [
    ...
    'generators' => [
        \Sowl\JsonApi\Scribe\JsonApiSpecGenerator::class,
    ],
],
```

### Configure the Custom Route Matcher

Add custom route matcher to Scribe:

```php
'routeMatcher' => \Sowl\JsonApi\Scribe\RouteMatcher::class,
```

### Configure Example Resource Instantiation Strategies

In `config/scribe.php`, set the `models_source` option in the `examples` section to use Doctrine strategies for generating example resources:

```php
'examples' => [
    ...
    'models_source' => [
        'doctrineFactoryCreate',
        // 'doctrineFactoryMake', // Optionally enable for non-persisted entities
        'doctrineRepositoryFirst',
    ],
],
```

This ensures Scribe will use Doctrine factories and repositories to generate example entities for your API docs. Uncomment `doctrineFactoryMake` if you want to allow non-persisted examples.

### Set Default Group to Null for Proper Group Naming

In `config/scribe.php`, set the default group to `null` so the library can automatically assign group names for endpoints without a `@group`:

```php
'groups' => [
    'default' => null,
    // ...
],
```

This allows Scribe to automatically group endpoints based on their controllers or other logic, making it easier to organize your API documentation.

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

The following PHP attributes are used by the strategies to extract documentation details. Click to view their definitions:

- [#[ResourceRequest]](ScribeAttributes.md#resourcerequest)
- [#[ResourceRequestList]](ScribeAttributes.md#resourcerequestlist)
- [#[ResourceRequestCreate]](ScribeAttributes.md#resourcerequestcreate)
- [#[ResourceRequestRelationships]](ScribeAttributes.md#resourcerequestrelationships)
- [#[ResourceResponse]](ScribeAttributes.md#resourceresponse)
- [#[ResourceResponseRelated]](ScribeAttributes.md#resourceresponserelated)
- [#[ResourceResponseRelationships]](ScribeAttributes.md#resourceresponserelationships)
- [#[ResourceMetadata]](ScribeAttributes.md#resourcemetadata)

These attributes are placed on controller methods to describe resource types, request/response structure, relationships, and headers for JSON:API endpoints. The strategies use them to generate accurate and detailed documentation.

#### Assigning Attributes to Custom Methods

To enable the strategies to extract documentation, you must assign the appropriate PHP attributes to your controller methods. Each attribute provides metadata about the request or response for that specific endpoint.

---

<details>
<summary>Show full example of using all Scribe JSON:API attributes</summary>

```php
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequestList;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequestCreate;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequestRelationships;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponseRelated;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponseRelationships;
use Sowl\JsonApi\Scribe\Attributes\ResourceMetadata;

#[ResourceMetadata(groupName: 'Users', groupDescription: 'Operations related to user resources.')]
class UserController
{
    #[ResourceRequest(resourceType: 'users', idType: 'string', idExample: 'abc123')]
    #[ResourceResponse(resourceType: 'users', status: 200, description: 'Get a user by ID')]
    public function show($id)
    {
        // ... your controller logic ...
    }

    #[ResourceRequestList(resourceType: 'users')]
    #[ResourceResponse(resourceType: 'users', collection: true, description: 'Get a list of users')]
    public function index()
    {
        // ... your controller logic ...
    }

    #[ResourceRequestCreate(resourceType: 'users')]
    #[ResourceResponse(resourceType: 'users', status: 201, description: 'Create a new user')]
    public function store($request)
    {
        // ... your controller logic ...
    }

    #[ResourceRequestRelationships(resourceType: 'users', idType: 'string', idExample: 'abc123', idParam: 'id')]
    #[ResourceResponseRelationships(resourceType: 'users', relationshipName: 'roles', collection: true, description: 'Get user roles relationship')]
    public function relationships($id, $relationship)
    {
        // ... your controller logic ...
    }

    #[ResourceRequest(resourceType: 'users', idType: 'string', idExample: 'abc123', idParam: 'id')]
    #[ResourceResponseRelated(resourceType: 'users', relationshipName: 'roles', collection: true, description: 'Get related roles for a user')]
    public function related($id, $relationship)
    {
        // ... your controller logic ...
    }
}
```

</details>

---

You can combine multiple attributes on a single method to describe all aspects of the endpoint. This approach allows the documentation generator to produce accurate and detailed API docs based on your code annotations.

> **Note:** Default controller and default traits already have the necessary attributes to generate documentation. That's why you don't need to add them manually.

### Metadata

Strategy:
[GetFromResourceMetadataAttribute](src/Scribe/Strategies/Metadata/GetFromResourceMetadataAttribute.php)

Extracts and documents metadata for each endpoint:
- Generates human-readable titles and descriptions for endpoints
- Groups endpoints by resource type and action (list, show, create, update, delete, relationships, etc.)
- Uses [#[ResourceMetadata]](src/Scribe/Attributes/ResourceMetadata.php) attribute for customization
- Ensures endpoints are clearly described and discoverable in the generated documentation

### Headers

Strategy:
[GetFromResourceAttributes](src/Scribe/Strategies/Headers/GetFromResourceAttributes.php)

Adds required JSON:API headers to endpoints using PHP attributes:
- `Accept: application/vnd.api+json`: Required for all requests
- `Content-Type: application/vnd.api+json`: Required for POST, PATCH, and DELETE requests
- Extracts header info from [#[ResourceRequest]](src/Scribe/Attributes/ResourceRequest.php), [#[ResourceResponse]](src/Scribe/Attributes/ResourceResponse.php), and related attributes

### Query Parameters

Strategy:
[GetFromResourceRequestAttributes](src/Scribe/Strategies/QueryParameters/GetFromResourceRequestAttributes.php)

Documents standard JSON:API query parameters for GET endpoints:
- `include`: Include related resources (comma-separated)
- `fields[type]`: Sparse fieldsets to select specific fields
- `sort`: Sort resources by fields (prefix with `-` for descending)
- `page[number]`, `page[size]`: Pagination controls
- `filter[field]`: Filter resources by field values (if supported by resource)
- Uses real filter, sort, and pagination definitions from resource classes
- Extracts parameters from [#[ResourceRequest]](src/Scribe/Attributes/ResourceRequest.php) and [#[ResourceRequestList]](src/Scribe/Attributes/ResourceRequestList.php) attributes
- Supports custom parameters defined in resource attributes

### Responses

Strategy:
[GetFromResourceResponseAttributes](src/Scribe/Strategies/Responses/GetFromResourceResponseAttributes.php)

Generates JSON:API compliant response examples:
- Shows proper response structure with `data`, `included`, and `meta` sections
- Includes relationship links and resource linkage
- Documents error responses in JSON:API format
- Provides examples for different HTTP status codes
- Uses real Doctrine entities and transformers for examples

### URL Parameters

Strategy:
[GetFromResourceRequestAttributes](src/Scribe/Strategies/UrlParameters/GetFromResourceRequestAttributes.php)

Documents URL parameters for JSON:API routes:
- `{id}`: Resource identifier (type and example derived from Doctrine metadata)
- `{resourceType}` and `{relationship}`: Expanded to all registered resource types and relationships
- Provides accurate descriptions and validation for each parameter

## Example Usage

Here's how these strategies work together to document a typical JSON:API endpoint:

```php
// Route definition
Route::get('/{resourceType}/{id}/relationships/{relationship}', 'JsonApiController@showRelationship');

// Generated documentation will include:
// - URL parameters: resourceType, id, relationship
// - Query parameters: include, fields, sort, page, filter, etc.
// - Headers: Accept, Content-Type
// - Response format: JSON:API compliant structure
// - Realistic examples using your entities and transformers
```

When processing these routes, the strategies will:

1. Convert dynamic placeholders like `{resourceType}` and `{relationship}` into actual registered resources and relationships
2. Generate documentation for each registered resource type (e.g., `users`, `articles`, `comments`)
3. Include real relationship examples based on the entity relationships defined in your application
4. Group endpoints by resource type, making the documentation more organized and intuitive
5. Use proper JSON:API structure in request and response examples, with real data from your entities

For example, a route like `/{resourceType}/{id}` might be documented as multiple concrete endpoints:
- `/users/{id}` - Get a specific user
- `/articles/{id}` - Get a specific article
- `/comments/{id}` - Get a specific comment

Similarly, relationship endpoints will be documented with actual relationship names from your entities.

## Response Documentation

The package generates realistic JSON:API compliant response examples for your API documentation using a combination of entity factories and transformers:

```php
$entity = entity($resourceClass)->create();

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

## Body Parameters Documentation

The package uses Laravel's validation rules to generate appropriate request body examples for your JSON:API endpoints.

### Validation Rules for Body Parameters

Body parameters are documented based on the validation rules defined in your application:

1. For POST/PATCH endpoints, validation rules determine required and optional fields
2. Field types (string, integer, boolean, etc.) are inferred from validation rules
3. Format constraints (email, date, URL, etc.) are respected in the examples
4. Relationship validation rules are used to structure relationship data correctly

For detailed information about validation rules, see our [Validation Documentation](./Validation.md) and [Scribe's documentation on validation rules](https://scribe.knuckles.wtf/laravel/documenting/query-body-parameters#validation-rules).

## Additional Resources

- [JSON:API Specification](https://jsonapi.org)
- [Scribe Documentation](https://scribe.knuckles.wtf/laravel/)
