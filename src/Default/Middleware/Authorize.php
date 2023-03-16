<?php

namespace Sowl\JsonApi\Default\Middleware;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Contracts\Auth\Access\Gate;
use Closure;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Exceptions\ForbiddenException;
use Sowl\JsonApi\Exceptions\JsonApiException;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\ResourceManager;

class Authorize
{
    public function __construct(
        protected Gate            $gate,
        protected ResourceManager $resourceManager,
    ) {}

    static protected array $methodResourceAbilityMap = [
        HttpRequest::METHOD_GET => AbilitiesInterface::SHOW,
        HttpRequest::METHOD_POST => AbilitiesInterface::CREATE,
        HttpRequest::METHOD_PATCH => AbilitiesInterface::UPDATE,
        HttpRequest::METHOD_DELETE => AbilitiesInterface::REMOVE,
    ];

    static protected array $methodRelationshipAbilityMap = [
        HttpRequest::METHOD_GET => AbilitiesInterface::SHOW,
        HttpRequest::METHOD_POST => AbilitiesInterface::ATTACH,
        HttpRequest::METHOD_PATCH => AbilitiesInterface::UPDATE,
        HttpRequest::METHOD_DELETE => AbilitiesInterface::DETACH,
    ];

    /**
     * @throws ForbiddenException
     * @throws JsonApiException
     */
    public function handle(HttpRequest $request, Closure $next, ...$args)
    {
        $ability = $this->guessAbility();
        $arguments = $this->buildArguments($args);

        if (!$this->gate->allows($ability, $arguments)) {
            $resourceType = $this->request()->resourceType();
            $message = sprintf('No "%s" ability on "%s" resource.', $ability, $resourceType);
            throw (new ForbiddenException($message))->forbiddenError($message);
        }

        return $next($request);
    }

    /**
     * Guess the ability by the request method and path.
     *
     * @throws ForbiddenException
     */
    protected function guessAbility(): string
    {
        $request = $this->request();
        $method = $request->method();

        if ($this->request()->getId()) {
            if (!empty($this->request()->relationshipName())) {
                if (!empty($ability = static::$methodRelationshipAbilityMap[$request->method()] ?? null)) {
                    $relationship = $this->request()->relationship();

                    if ($relationship instanceof ToManyRelationship && $ability === AbilitiesInterface::SHOW) {
                        $ability = AbilitiesInterface::LIST;
                    }

                    return $ability . ucfirst($relationship->name());
                }
            }

            if (!empty($ability = static::$methodResourceAbilityMap[$request->method()] ?? null)) {
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
     * @throws JsonApiException
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
