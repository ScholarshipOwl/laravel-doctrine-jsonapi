<?php

namespace App\Transformers;

use App\Entities\UserStatus;
use Sowl\JsonApi\AbstractTransformer;

class UserStatusTransformer extends AbstractTransformer
{
    public function transform(UserStatus $userStatus): array
    {
        return [
            'id' => $userStatus->getId(),
            'name' => $userStatus->getName(),
        ];
    }
}
