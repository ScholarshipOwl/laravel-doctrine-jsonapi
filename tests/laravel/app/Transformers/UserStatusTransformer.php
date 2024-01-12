<?php

namespace Tests\App\Transformers;

use Sowl\JsonApi\AbstractTransformer;
use Tests\App\Entities\UserStatus;

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
