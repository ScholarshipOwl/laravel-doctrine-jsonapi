<?php

namespace Tests\App\Http\Controller;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Sowl\JsonApi\Action\Resource\ShowResource;
use Sowl\JsonApi\JsonApiResponse;
use Tests\App\Actions\User\ShowUserRequest;
use Tests\App\Entities\User;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->authorizeResource(User::class);
    }

    public function show(ShowUserRequest $request): JsonApiResponse
    {
        return (new ShowResource())->dispatch($request);
    }

    /**
     * Get the map of resource methods to ability names.
     *
     * @return array
     */
    protected function resourceAbilityMap()
    {
        return [
            'list' => 'list',
            'show' => 'show',
            'create' => 'create',
            'update' => 'update',
            'remove' => 'remove',
        ];
    }

    /**
     * Get the list of resource methods which do not have model parameters.
     *
     * @return array
     */
    protected function resourceMethodsWithoutModels()
    {
        return ['list', 'create'];
    }
}