<p align="center">
  <img src="../images/openapi/hero.png" alt="Hello" style="width: 100%; border-top-left-radius: 2rem; border-top-right-radius: 2rem;" />
</p>

API documentation is an essential part of any API. It provides a clear understanding of the endpoints,
their parameters, expected responses and errors. With this documentation, developers can easily consume
the API, without having to contact the API developers directly. It also helps to
reduce the number of support requests, since the documentation answers many of the questions that
developers may have.

This package provides custom [Scribe](https://scribe.knuckles.wtf/laravel/) strategies to document your JSON:API endpoints automatically in [OpenAPI](https://www.openapis.org/) format.

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

> **Important:** Before configuring, you must read the official Scribe configuration guide: [Scribe config reference](https://scribe.knuckles.wtf/laravel/reference/config).
>
For configuration and setup details specific to this package, see [ScribeConfiguration.md](./ScribeConfiguration.md).

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
3. For responses, entity factories create sample entities or select one from the database
4. Transformers convert the entities to JSON:API format
5. The documentation shows both valid request examples and expected responses

This integration with factories and validation rules ensures that your API documentation accurately reflects your application's data model and validation constraints.

## Strategies Overview

> A **strategy** in Scribe is a way to customize or extend how Scribe generates API documentation. Strategies are plugins that allow you to control how different parts of your API (like query parameters, responses, headers, etc.) are documented. You can use the built-in strategies, extend them, or write your own to fit your needs. For more details, see the official Scribe documentation: [Creating a Strategy](https://scribe.knuckles.wtf/laravel/advanced/plugins#creating-a-strategy).

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

For a full reference of the strategies, see [Scribe JSON:API Strategies](ScribeStrategies.md).

## Routes Matcher

Routes matcher is a custom implementation of the Scribe route matcher that extends the default behavior to handle JSON:API specific routes and this library dynamic routes.

Convert dynamic placeholders like `{resourceType}` and `{relationship}` into actual registered resources and relationships

For example, a route like `/{resourceType}/{id}` might be documented as multiple concrete endpoints:
- `/users/{id}` - Get a specific user
- `/articles/{id}` - Get a specific article
- `/comments/{id}` - Get a specific comment
- `/users/{id}/relationships/roles` - Get a user roles relationship
- `/users/{id}/roles` - Get related roles for a user

## Examples Factory

The package generates realistic JSON:API compliant response examples for your API documentation using a combination of entity factories and transformers:

```php
$entity = entity($resourceClass)->create();
$transformer = $this->getTransformerForResource($resourceType);
$response = $this->response()->item($entity, $transformer);
```

When generatin documentation Scribe needs to have connection to the database to generate realistic response examples. Generation runs in a transaction to avoid affecting your development database. We recommend using an SQLite in-memory database for documentation generation if possible also you can have a separate environment configuration for documentation generation.

Strategy provides 3 types of example sourcing (see config: `scribe.examples.models_source`):
- `doctrineFactoryCreate` - Use the model's factory to create an instance
- `doctrineRepositoryFirst` - Use the first instance from the database
- `doctrineFactoryMake` - Use the model's factory to make an instance but not persist it (not working well with JSON:API)


This process ensures:
- Response examples contain realistic data that matches your entity structure
- Relationships between entities are properly represented
- Query parameters options included in documentation (fields, sort, etc.)
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

## Additional Resources

- [JSON:API Specification](https://jsonapi.org)
- [Scribe Documentation](https://scribe.knuckles.wtf/laravel/)
