<?php

namespace Sowl\JsonApi\AbstractTransformer;

use Illuminate\Support\Facades\Gate;
use Tests\App\Entities\User;
use Illuminate\Support\Str;
use League\Fractal\Resource\Collection;
use Sowl\JsonApi\AbstractTransformer;
use Tests\App\Entities\Role;
use Tests\App\Transformers\RoleTransformer;

class UserTransformer extends AbstractTransformer
{
    protected array $availableIncludes = [
        'roles'
    ];

    protected array $availableMetas = [
        'random',
    ];

    public function transform(User $user): array
    {
        return [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getEmail(),
        ];
    }

    public function includeRoles(User $user): Collection
    {
        Gate::authorize('listRoles', $user);

        return $this->collection($user->getRoles(), new RoleTransformer(), Role::getResourceType());
    }

    public function metaRandom(User $user): string
    {
        return $user->getName() . Str::random(8);
    }
}
