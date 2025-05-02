# AbstractAction

**Location:** `src/AbstractAction.php`

The `AbstractAction` class is the base for all resource actions (Show, List, Create, Update, Remove, etc.).

## Responsibilities
- Handles request injection, response formatting, and error handling for actions.
- Provides helper methods for working with the entity manager, resource manager, and resource manipulator.

## Key Methods
- `create(...$args)`: Static factory for action instances.
- `dispatch(Request $request)`: Handles request and returns a response.
- `handle()`: Must be implemented in subclasses to perform the action logic.
- `em()`: Returns the Doctrine EntityManager.
- `rm()`: Returns the ResourceManager instance.
- `manipulator()`: Returns the ResourceManipulator instance.
- `response()`: Returns the ResponseFactory instance.

## Example Usage
```php
class ShowUserAction extends AbstractAction {
    public function handle(): Response {
        $user = $this->request()->resource();
        return $this->response()->item($user);
    }
}
```

## Extension Points
- Extend this class to implement custom actions for your resources.
