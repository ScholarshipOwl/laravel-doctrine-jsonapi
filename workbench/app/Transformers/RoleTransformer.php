<?php

namespace App\Transformers;

use App\Entities\Role;
use Sowl\JsonApi\AbstractTransformer;

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
