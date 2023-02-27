<?php

namespace Sowl\JsonApi\Middleware;

use Sowl\JsonApi\Exceptions\ForbiddenException;
use Sowl\JsonApi\Exceptions\JsonApiException;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\ResourceManager;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Contracts\Auth\Access\Gate;
use Closure;

class Authorize
{
    public function __construct(
        protected Gate            $gate,
        protected ResourceManager $resourceManager,
    ) {}

    /**
     * @throws ForbiddenException
     * @throws JsonApiException
     */
    public function handle(HttpRequest $request, Closure $next, $ability, ...$args)
    {
        $arguments = $this->getGateArguments($args);

        if (!$this->gate->allows($ability, $arguments)) {
            $resourceKey = $this->request()->resourceKey();
            $message = sprintf('No "%s" ability on "%s" resource.', $ability, $resourceKey);
            throw (new ForbiddenException($message))->forbiddenError($message);
        }

        return $next($request);
    }

    /**
     * @throws JsonApiException
     */
    protected function getGateArguments(array $arguments): array
    {
        $resourceKey = $this->request()->resourceKey();
        $resourceClass = $this->rm()->classByResourceKey($resourceKey);
        $baseArguments = [$resourceClass];

        if ($id = $this->request()->getId()) {
            $repo = $this->rm()->repositoryByClass($resourceClass);
            $resource = $repo->findById($id);
            $baseArguments = [$resource];
        }

        if ($relationshipName = $this->request()->relationshipName()) {
            $baseArguments[] = $relationshipName;
        }

        return array_merge($baseArguments, $arguments);
    }

    protected function request(): Request
    {
        return app(Request::class);
    }

    protected function rm(): ResourceManager
    {
        return $this->resourceManager;
    }
}
