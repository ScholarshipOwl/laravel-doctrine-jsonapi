<?php

namespace Sowl\JsonApi;

use Doctrine\ORM\EntityManager;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Access\Gate;
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
 * @method Response handle() The handle method must be implemented, but we do not define it as abstract
 *                           because we PHP do not allow arguments override , and we want to use laravel
 *                           dependency injection container to get dependencies.
 */
abstract class AbstractAction
{
    protected bool $disableAuthorization = false;

    public static function make(...$args): static
    {
        /** @phpstan-ignore new.static */
        return new static(...$args);
    }

    public static function makeDispatch(...$args): Response
    {
        return static::make(...$args)->dispatch();
    }

    /**
     * The dispatch method must be called with provided request.
     * Dispatch method will call the handle method injecting its dependencies and will return JsonApiResponse.
     * JsonApiException and Authentication exceptions handled and error response build from them.
     */
    public function dispatch(): Response
    {
        try {
            if (! $this->disableAuthorization && method_exists($this, 'authorize')) {
                app()->call([$this, 'authorize']);
            }

            return app()->call([$this, 'handle']);
        } catch (AuthorizationException $e) {
            return $this->response()->exception(new ForbiddenException(
                previous: $e
            ));
        } catch (JsonApiException $e) {
            return $this->response()->exception($e);
        }
    }

    public function disableAuthorization(): static
    {
        $this->disableAuthorization = true;

        return $this;
    }

    /**
     * Returns the entity manager for the current resource.
     */
    protected function em(): EntityManager
    {
        return app(EntityManager::class);
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

    protected function response(): ResponseFactory
    {
        return app(ResponseFactory::class);
    }

    protected function gate(): Gate
    {
        return app(Gate::class);
    }
}
