<?php namespace Tests\App\Transformers;

use Illuminate\Support\Str;
use League\Fractal\Resource\Collection;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\AuthenticationAbilitiesInterface;
use Tests\App\Entities\Role;
use Tests\App\Entities\User;

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
        $this->gate()->authorize(AuthenticationAbilitiesInterface::LIST_RELATIONSHIPS, [$user, Role::class]);

        return $this->collection($user->getRoles(), new RoleTransformer(), Role::getResourceKey());
    }

    public function metaRandom(User $user): string
    {
        return $user->getName() . Str::random(8);
    }
}
