<?php

declare(strict_types=1);

namespace Tests\App\Transformers;

use League\Fractal\TransformerAbstract;
use Tests\App\Entities\UserConfig;

class UserConfigTransformer extends TransformerAbstract
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
        return $this->item($userConfig->getUser(), new UserTransformer(), 'users');
    }
}
