# JSON:API Documentation with Scribe

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

## Configuration

To use the JSON:API custom strategies, you need to add them to your Scribe configuration. In your `config/scribe.php` file, add our strategies to the corresponding sections:

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
    'bodyParameters' => [
        // ...existing strategies
        \Sowl\JsonApi\Scribe\BodyParameters\GetFromJsonApiRouteStrategy::class,
    ],
    'responses' => [
        // ...existing strategies
        \Sowl\JsonApi\Scribe\Responses\UseJsonApiResourceResponseStrategy::class,
    ],
],
```

## Strategies Overview

This package provides various strategies for documenting JSON:API endpoints:

### Metadata Strategies

- `GetFromJsonApiRouteStrategy`: Extracts metadata (title, description) from JSON:API routes, grouping them together in the documentation.

### URL Parameters Strategies

- `GetFromJsonApiRouteStrategy`: Documents URL parameters for JSON:API routes (`id`, `resourceType`, `relationship`).

### Query Parameters Strategies 

- `AddJsonApiQueryParametersStrategy`: Adds standard JSON:API query parameters to GET routes:
  - `filter`: For filtering resources
  - `sort`: For sorting resources 
  - `page[number]`, `page[size]`, etc.: For pagination
  - `include`: For including related resources
  - `fields`: For sparse fieldsets

### Headers Strategies

- `AddJsonApiHeadersStrategy`: Adds JSON:API content type and accept headers.

### Body Parameters Strategies

- `GetFromJsonApiRouteStrategy`: Adds appropriate JSON:API request body structure for create, update, and relationship operations.

### Response Strategies

- `UseJsonApiResourceResponseStrategy`: Generates JSON:API compliant response examples.

## Doctrine Entity Extractor

The package also includes a `DoctrineEntityExtractor` class that can extract schema information from your Doctrine entities for documentation purposes. This can be useful when documenting payload formats:

```php
use Sowl\JsonApi\Scribe\DoctrineEntityExtractor;

// In a controller or custom strategy
$extractor = app(DoctrineEntityExtractor::class);
$schema = $extractor->extractEntitySchema(YourEntity::class);
```

## Dynamic Routes

For routes with dynamic resource types (e.g., `/{resourceType}/{id}`), our strategies provide documentation based on the route pattern and common JSON:API conventions.

## Example Documentation

After configuring Scribe with our strategies, generate the documentation:

```bash
php artisan scribe:generate
```

The generated documentation will include:

- JSON:API compliant request and response formats
- All available query parameters for filtering, sorting, and pagination
- Proper headers for JSON:API compatibility
- Relationship handling examples

## Additional Resources

- [JSON:API Specification](https://jsonapi.org)
- [Scribe Documentation](https://scribe.knuckles.wtf/laravel/)