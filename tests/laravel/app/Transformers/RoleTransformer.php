<?php

namespace Tests\App\Transformers;

use Sowl\JsonApi\AbstractTransformer;
use Tests\App\Entities\Role;

class RoleTransformer extends AbstractTransformer
{
    public function transform(Role $role): array
    {
        return [
            'id' => $role->getId(),
            'name' => $role->getName(),
        ];
    }
}
