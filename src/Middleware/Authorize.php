<?php

namespace Sowl\JsonApi\Middleware;

use Illuminate\Http\Request;
use Sowl\JsonApi\AbstractRequest;
use Sowl\JsonApi\Exceptions\ForbiddenException;
use Sowl\JsonApi\Exceptions\JsonApiException;
use Sowl\JsonApi\JsonApiResponse;

use Illuminate\Contracts\Auth\Access\Gate;
use Closure;
use Sowl\JsonApi\ResourceManager;

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
    public function handle(Request $request, Closure $next, $ability, ...$args): JsonApiResponse
    {
        $arguments = $this->getGateArguments($request, $args);

        if (!$this->gate->allows($ability, $arguments)) {
            $resourceKey = $this->resourceKey($request);
            $message = sprintf('No "%s" ability on "%s" resource.', $ability, $resourceKey);
            throw (new ForbiddenException($message))->forbiddenError($message);
        }

        return $next($request);
    }

    /**
     * @throws JsonApiException
     */
    protected function getGateArguments(Request $request, array $arguments): array
    {
        $resourceKey = $this->resourceKey($request);

        if ($id = $request->route('id')) {
            $repo = $this->resourceManager->repositoryByResourceKey($resourceKey);
            $resource = $repo->findById($id);

            return [$resource];
        }

        return [$this->resourceManager->classByResourceKey($resourceKey)];
    }

    /**
     * @throws JsonApiException
     */
    public function resourceKey(Request $request): string
    {
        if (null !== ($resourceKey = $request->route('resourceKey'))) {
            return $resourceKey;
        }

        $matches = [];
        if (preg_match('/^([^\/.]*)\/?.*$/', $request->path(), $matches)) {
            return $matches[1];
        }

        throw JsonApiException::create('No resource key found for the request', 404);
    }
}