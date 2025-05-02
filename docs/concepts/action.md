# Actions

Actions are the core units of business logic and data manipulation in the Laravel Doctrine JSON:API package. Each action encapsulates a single operation—such as showing, creating, updating, or deleting a resource—and is responsible for handling requests, performing validation, and returning JSON:API-compliant responses.

## Key Concepts
- **Action Classes:** Actions extend the `AbstractAction` base class, which provides helper methods for working with the request, entity manager, resource manager, and response factory.
- **Separation of Concerns:** Actions isolate business logic from controllers, making your codebase easier to maintain and test.
- **Types of Actions:** Common actions include `ShowResourceAction`, `ListResourcesAction`, `CreateResourceAction`, `UpdateResourceAction`, and `RemoveResourceAction`. You can also create custom actions for advanced use cases.

## Example: Custom Action
```php
class ActivateUserAction extends AbstractAction {
    public function handle(): Response {
        $user = $this->request()->resource();
        $user->activate();
        $this->em()->flush();
        return $this->response()->item($user);
    }
}
```

## Best Practices
- Keep each action focused on a single responsibility.
- Use dependency injection and helper methods provided by `AbstractAction`.
- Write tests for your actions to ensure correctness and reliability.
- Register custom actions as needed to extend API functionality.
