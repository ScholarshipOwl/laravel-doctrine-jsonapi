<?php

namespace Sowl\JsonApi\Default\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Http\Request as HttpRequest;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Exceptions\ForbiddenException;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\ResourceManager;

/**
 * Middleware is responsible for authorizing the actions performed on resources and relationships.
 *
 * It uses the Laravel's Gate contract to check if the user has the necessary permissions to perform the requested
 * action on the given resource or relationship.
 *
 * Middleware is useful for enforcing access control in your JSON:API implementation, ensuring that users can only
 * perform actions they have the necessary permissions for. By using this middleware, you can prevent unauthorized
 * access to your resources and relationships.
 */
class Authorize
{
    public function __construct(
        protected Gate $gate,
        protected ResourceManager $resourceManager,
    ) {}

    protected static array $methodResourceAbilityMap = [
        HttpRequest::METHOD_GET => AbilitiesInterface::SHOW,
        HttpRequest::METHOD_POST => AbilitiesInterface::CREATE,
        HttpRequest::METHOD_PATCH => AbilitiesInterface::UPDATE,
        HttpRequest::METHOD_DELETE => AbilitiesInterface::REMOVE,
    ];

    protected static array $methodRelationshipAbilityMap = [
        HttpRequest::METHOD_GET => AbilitiesInterface::SHOW,
        HttpRequest::METHOD_POST => AbilitiesInterface::ATTACH,
        HttpRequest::METHOD_PATCH => AbilitiesInterface::UPDATE,
        HttpRequest::METHOD_DELETE => AbilitiesInterface::DETACH,
    ];

    /**
     * Method receives an HttpRequest, a Closure for the next middleware in the stack, and additional arguments.
     *
     * It checks if the user has the required ability for the requested action by calling the guessAbility method.
     * If the user has the required ability, the request proceeds to the next middleware in the stack; otherwise,
     * it throws a ForbiddenException.
     */
    public function handle(HttpRequest $request, Closure $next, ...$args)
    {
        $ability = $this->guessAbility();
        $arguments = $this->buildArguments($args);

        if (! $this->gate->allows($ability, $arguments)) {
            $resourceType = $this->request()->resourceType();
            $message = sprintf('No "%s" ability on "%s" resource.', $ability, $resourceType);
            throw (new ForbiddenException($message))->forbiddenError($message);
        }

        return $next($request);
    }

    /**
     * Method determines the required ability based on the request's method and path.
     * It maps the request's method to the corresponding ability for resources and relationships.
     */
    protected function guessAbility(): string
    {
        $request = $this->request();
        $method = $request->method();

        if ($this->request()->getId()) {
            if (! empty($this->request()->relationshipName())) {
                if (! empty($ability = static::$methodRelationshipAbilityMap[$request->method()] ?? null)) {
                    $relationship = $this->request()->relationship();

                    if ($relationship instanceof ToManyRelationship && $ability === AbilitiesInterface::SHOW) {
                        $ability = AbilitiesInterface::LIST;
                    }

                    return $ability.ucfirst($relationship->name());
                }
            }

            if (! empty($ability = static::$methodResourceAbilityMap[$request->method()] ?? null)) {
                return $ability;
            }
        } elseif ($request->method() === HttpRequest::METHOD_GET) {
            return AbilitiesInterface::LIST;
        } elseif ($request->method() === HttpRequest::METHOD_POST) {
            return AbilitiesInterface::CREATE;
        }

        throw ForbiddenException::create()
            ->error(403, [], sprintf('Can\'t guess the ability for the method "%s"', $method));
    }

    /**
     * Method constructs an array of arguments for the Gate to check if the user is authorized.
     *
     * It fetches the appropriate repository and finds the resource using the provided ID, if present.
     * Otherwise, it uses the class name of the repository.
     *
     * The method then merges this base argument with any additional arguments passed to the middleware.
     */
    protected function buildArguments(array $arguments): array
    {
        $repo = $this->request()->repository();

        if ($id = $this->request()->getId()) {
            $baseArgument = $repo->findById($id);
        } else {
            $baseArgument = $repo->getClassName();
        }

        return array_merge([$baseArgument], $arguments);
    }

    protected function request(): Request
    {
        return app(Request::class);
    }
}
