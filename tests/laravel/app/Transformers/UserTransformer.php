<?php namespace Tests\App\Transformers;

use League\Fractal\Resource\Collection;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\AbilitiesInterface;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

class UserTransformer extends AbstractTransformer
{
    protected array $availableIncludes = [
        'roles'
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
        $this->gate()->authorize(AbilitiesInterface::LIST_RELATIONSHIPS, [$user, Role::class]);

        return $this->collection($user->getRoles(), new RoleTransformer(), Role::getResourceKey());
    }
}
