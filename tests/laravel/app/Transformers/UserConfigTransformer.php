<?php

declare(strict_types=1);

namespace Tests\App\Transformers;

use Sowl\JsonApi\AbstractTransformer;
use Tests\App\Entities\User;
use Tests\App\Entities\UserConfig;

class UserConfigTransformer extends AbstractTransformer
{
    protected array $availableIncludes = ['user'];

    /**
     * @param UserConfig $userConfig
     */
    public function transform(UserConfig $userConfig): array
    {
        return [
            'id' => $userConfig->getUser()->getId(), // ID comes from the related User
            'theme' => $userConfig->getTheme(),
            'notificationsEnabled' => $userConfig->isNotificationsEnabled(),
            'language' => $userConfig->getLanguage(),
        ];
    }

    /**
     * @param UserConfig $userConfig
     */
    public function includeUser(UserConfig $userConfig): \League\Fractal\Resource\Item
    {
        return $this->resource($userConfig->getUser());
    }
}
