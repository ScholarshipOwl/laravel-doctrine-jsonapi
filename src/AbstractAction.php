<?php namespace Sowl\JsonApi;

use Doctrine\ORM\EntityManager;
use Illuminate\Auth\Access\AuthorizationException;
use Sowl\JsonApi\Exceptions\ForbiddenException;
use Sowl\JsonApi\Exceptions\JsonApiException;

/**
 * Any JSON:API endpoint handler should inherit this class.
 * It has all the dependencies required for any action to be implemented.
 *
 * Such as doctrine repository, transformer, manipulator.
 *
 * The dispatch method must be called with provided request.
 * Dispatch method will call the handle method injecting its dependencies and will return JsonApiResponse.
 *
 * @method Response handle(...$args) The handle method must be implemented, but we do not define it as abstract
 *                                          because we PHP do not allow arguments override , and we want to use laravel
 *                                          dependency injection container to get dependencies.
 */
abstract class AbstractAction
{
    protected Request $request;

    public static function create(...$args): static
    {
        return new static(...$args);
    }

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

    protected function repository(): ResourceRepository
    {
        return $this->request->repository();
    }

    protected function request(): Request
    {
        return $this->request;
    }

    protected function rm(): ResourceManager
    {
        return app(ResourceManager::class);
    }

    protected function manipulator(): ResourceManipulator
    {
        return app(ResourceManipulator::class);
    }

    protected function em(): EntityManager
    {
        return $this->repository()->em();
    }
}
