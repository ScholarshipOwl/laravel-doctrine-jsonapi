# ResourceManager

**Location:** `src/ResourceManager.php`

The `ResourceManager` is the central registry and service for managing all JSON:API resources in your application.

## Responsibilities
- Registers and manages resource classes that implement `ResourceInterface`.
- Provides lookup and helper methods for resource types, transformers, and repositories.
- Used internally by controllers, actions, and middleware to resolve resources and related components.

## Key Methods
- `registerResource($class)`: Register a resource class.
- `resources()`: Get all registered resources.
- `classByResourceType($type)`: Get the class name for a resource type.
- `transformerByResourceType($type)`: Get the transformer for a resource type.
- `repositoryByClass($class)`: Get the Doctrine repository for a resource class.

## Example Usage
```php
$rm = app(ResourceManager::class);
$rm->registerResource(App\Entities\User::class);
$allResources = $rm->resources();
```

## Extension Points
- Add your custom resources by registering them in the `resources` array in your config.
- Extend the class if you need to customize resource management logic.
