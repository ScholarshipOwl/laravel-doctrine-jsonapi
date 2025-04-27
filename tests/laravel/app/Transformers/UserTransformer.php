<?php

namespace Tests\App\Transformers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\ResourceInterface;
use Sowl\JsonApi\AbstractTransformer;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;
use Tests\App\Entities\UserStatus;

class UserTransformer extends AbstractTransformer
{
    protected array $availableIncludes = [
        'status',
        'roles',
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
        Gate::authorize('viewAnyRoles', $user);

        return $this->collection($user->getRoles(), new RoleTransformer, Role::getResourceType());
    }

    public function metaRandom(User $user): string
    {
        return $user->getName().Str::random(8);
    }

    public function includeStatus(User $user): ResourceInterface
    {
        return $this->item($user->getStatus(), new UserStatusTransformer, UserStatus::getResourceType());
    }
}
