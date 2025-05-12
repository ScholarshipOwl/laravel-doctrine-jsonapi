# JSON:API Package Configuration

This guide explains each configuration option available in `config/jsonapi.php` for Laravel Doctrine JSON:API. Understanding these fields helps you tailor the package to your application's needs.

## `resources`
**Type:** `array of class names`

A list of Doctrine entity classes to be managed as JSON:API resources. Each class must implement the [`ResourceInterface`](../src/ResourceInterface.php) provided by the package. These resources will automatically have endpoints registered and be handled by the [`ResourceManager`](../src/ResourceManager.php).

**Example:**
```php
'resources' => [
    App\Entities\User::class,
],
```

## `routing`

Routing-related configuration for all JSON:API endpoints.

### `routing.name`
**Type:** `string`  
Default: `'jsonapi.'`

A prefix for all JSON:API route names. This helps avoid naming collisions and makes route referencing more consistent.

### `routing.prefix`
**Type:** `string`  
Default: `'api'`

A URL prefix for all JSON:API routes. Set this if you want your API endpoints to be nested under a specific path (e.g., `/api`).

## `scribe`
**Type:** `array`

Configuration for the [Scribe](https://scribe.knuckles.wtf/) package if you are using the package's strategies for API documentation.

### `scribe.middleware`
**Type:** `string`  
Default: `'api'`

The middleware assigned to all JSON:API routes. Used to identify JSON:API routes in Scribe documentation generation. If you change the value of your API prefix in `bootstrap/app.php`, make sure to update the value of `middleware` here as well.

**Example:**
```php
'scribe' => [
    'middleware' => 'api',
],
```
