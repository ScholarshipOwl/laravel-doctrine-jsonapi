<?php

namespace Sowl\JsonApi;

use Sowl\JsonApi\Controller\AbstractController;
use Sowl\JsonApi\Controller\WithListTrait;
use Sowl\JsonApi\Controller\WithRelatedTrait;
use Sowl\JsonApi\Controller\WithRelationshipTrait;
use Sowl\JsonApi\Controller\WithRemoveRelationshipsTrait;
use Sowl\JsonApi\Controller\WithRemoveTrait;
use Sowl\JsonApi\Controller\WithShowTrait;

class Controller extends AbstractController
{
    use WithShowTrait;
    use WithRemoveTrait;
    use WithListTrait;

    use WithRelatedTrait;
    use WithRelationshipTrait;
    use WithRemoveRelationshipsTrait;

    /**
     * @inheritdoc
     */
    protected function methodToAbilityMap(): array
    {
        return [
            'show' => AuthenticationAbilitiesInterface::SHOW_RESOURCE,
            'create' => AuthenticationAbilitiesInterface::CREATE_RESOURCE,
            'update' => AuthenticationAbilitiesInterface::UPDATE_RESOURCE,
            'remove' => AuthenticationAbilitiesInterface::REMOVE_RESOURCE,
            'list' => AuthenticationAbilitiesInterface::LIST_RESOURCES,

            'related' => AuthenticationAbilitiesInterface::SHOW_RELATIONSHIPS,
            'relationship' => AuthenticationAbilitiesInterface::SHOW_RELATIONSHIPS,
            'removeRelationships' => AuthenticationAbilitiesInterface::REMOVE_RELATIONSHIPS,
        ];
    }
}
