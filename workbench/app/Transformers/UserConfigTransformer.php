<?php

declare(strict_types=1);

namespace App\Transformers;

use App\Entities\User;
use App\Entities\UserConfig;
use Sowl\JsonApi\AbstractTransformer;

class UserConfigTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['user'];

    public function transform(UserConfig $userConfig): array
    {
        return [
            'id' => $userConfig->getUser()->getId(), // ID comes from the related User
            'theme' => $userConfig->getTheme(),
            'notificationsEnabled' => $userConfig->isNotificationsEnabled(),
            'language' => $userConfig->getLanguage(),
        ];
    }

    public function includeUser(UserConfig $userConfig): \League\Fractal\Resource\Item
    {
        return $this->resource($userConfig->getUser());
    }
}
