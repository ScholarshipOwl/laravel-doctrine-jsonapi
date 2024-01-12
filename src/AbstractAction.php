<?php

namespace Sowl\JsonApi;

use Doctrine\ORM\EntityManager;
use Illuminate\Auth\Access\AuthorizationException;
use Sowl\JsonApi\Exceptions\ForbiddenException;
use Sowl\JsonApi\Exceptions\JsonApiException;

/**
 * Any JSON:API endpoint handler should inherit this class.
 * It has all the dependencies required for any action to be implemented.
 * Such as EntityManager, ResourceManager, ResourceRepository, ResourceManipulator and JsonApi Request.
 *
 * The "dispatch" method handles the request and generates response.
 * It's calling the "handle" method that can inject dependencies from Laravel DI.
 *
 * @method Response handle(...$args) The handle method must be implemented, but we do not define it as abstract
 *                                   because we PHP do not allow arguments override , and we want to use laravel
 *                                   dependency injection container to get dependencies.
 */
abstract class AbstractAction
{
    protected Request $request;

    /**
     * Helper for new action object construction.
     */
    public static function create(...$args): static
    {
        return new static(...$args);
    }

    /**
     * The dispatch method must be called with provided request.
     * Dispatch method will call the handle method injecting its dependencies and will return JsonApiResponse.
     * JsonApiException and Authentication exceptions handled and error response build from them.
     */
    public function dispatch(Request $request): Response
    {
        $this->request = $request;

        try {
            return app()->call([$this, 'handle']);
        } catch (AuthorizationException $e) {
            return response()->exception(new ForbiddenException(
                previous: $e
            ));
        } catch (JsonApiException $e) {
            return response()->exception($e);
        }
    }

    /**
     * Returns the current JSON:API request.
     */
    protected function request(): Request
    {
        return $this->request;
    }

    /**
     * Get the resource repository from the request.
     */
    protected function repository(): ResourceRepository
    {
        return $this->request->repository();
    }

    /**
     * Returns the entity manager for the current resource.
     */
    protected function em(): EntityManager
    {
        return $this->repository()->em();
    }

    /**
     * Returns the resource manager instance.
     */
    protected function rm(): ResourceManager
    {
        return app(ResourceManager::class);
    }

    /**
     * Returns the resource manipulator instance.
     */
    protected function manipulator(): ResourceManipulator
    {
        return app(ResourceManipulator::class);
    }
}
