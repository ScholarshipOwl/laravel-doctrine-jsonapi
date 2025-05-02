# Scribe JSON:API Configuration Guide

This guide explains how to configure Scribe for Laravel Doctrine JSON:API projects, ensuring your API documentation is accurate, modern, and leverages all advanced features of this package.

---

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

---

### Docs Type and Theme

For this skeleton, we recommend and use the `external_laravel` type, which serves the OpenAPI spec with an advanced external UI (Scalar):

```php
'type' => 'external_laravel',
'theme' => 'scalar',
```

This provides a modern, feature-rich OpenAPI UI for your documentation at `/docs` (or the configured docs URL).

---

### Restricting Access to Docs

You can restrict access to the API docs by adding middleware (e.g., `['auth']`) in the `laravel.middleware` config section:

```php
'laravel' => [
    ...
    'middleware' => ['auth'], // Only authenticated users can view docs
],
```

You may also use authorization middleware for fine-grained access control.

---

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

---

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

---

### Configure the Custom Route Matcher

Add custom route matcher to Scribe:

```php
'routeMatcher' => \Sowl\JsonApi\Scribe\RouteMatcher::class,
```

---

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

---

### Set Default Group to Null for Proper Group Naming

In `config/scribe.php`, set the default group to `null` so the library can automatically assign group names for endpoints without a `@group`:

```php
'groups' => [
    'default' => null,
    // ...
],
```

This allows Scribe to automatically group endpoints based on their controllers or other logic, making it easier to organize your API documentation.
