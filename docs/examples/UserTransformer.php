<?php

namespace Sowl\JsonApi\AbstractTransformer;

use App\Entities\Role;
use App\Entities\User;
use App\Transformers\RoleTransformer;
use App\Transformers\UserConfigTransformer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use League\Fractal\Resource\Collection;
use Sowl\JsonApi\AbstractTransformer;

class UserTransformer extends AbstractTransformer
{
    protected array $availableIncludes = [
        'roles',
        'config',
    ];

    protected array $defaultIncludes = [
        'config',
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
        return $user->getName().Str::random(8);
    }

    public function includeConfig(User $user)
    {
        // Assuming User has getUserConfig method
        return $this->item($user->getUserConfig(), new UserConfigTransformer(), 'user-configs');
    }
}
