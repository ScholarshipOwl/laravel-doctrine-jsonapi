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
**Type:** `array`

Routing-related configuration for all JSON:API endpoints.

### `routing.rootMiddleware`
**Type:** `string|array`  
Default: `'jsonapi'`

The middleware (or middleware group) applied to all JSON:API routes. Use this to add authentication, throttling, or other middleware as needed.

### `routing.rootNamePrefix`
**Type:** `string`  
Default: `'jsonapi.'`

A prefix for all JSON:API route names. This helps avoid naming collisions and makes route referencing more consistent.

### `routing.rootPathPrefix`
**Type:** `string`  
Default: `''`

A URL prefix for all JSON:API routes. Set this if you want your API endpoints to be nested under a specific path (e.g., `/api`).

**Example:**
```php
'routing' => [
    'rootMiddleware' => 'jsonapi',
    'rootNamePrefix' => 'jsonapi.',
    'rootPathPrefix' => '',
],
```
