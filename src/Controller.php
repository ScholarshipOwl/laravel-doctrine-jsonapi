<?php

namespace Sowl\JsonApi;

use Sowl\JsonApi\Controller\AbstractController;
use Sowl\JsonApi\Controller\WithCreateRelationshipsTrait;
use Sowl\JsonApi\Controller\WithRemoveTrait;
use Sowl\JsonApi\Controller\WithShowTrait;
use Sowl\JsonApi\Controller\WithListTrait;
use Sowl\JsonApi\Controller\WithRelatedTrait;
use Sowl\JsonApi\Controller\WithRelationshipTrait;
use Sowl\JsonApi\Controller\WithRemoveRelationshipsTrait;
use Sowl\JsonApi\Controller\WithUpdateRelationshipsTrait;

class Controller extends AbstractController
{
    use WithShowTrait;
    use WithRemoveTrait;
    use WithListTrait;

    use WithRelatedTrait;
    use WithRelationshipTrait;
    use WithUpdateRelationshipsTrait;
    use WithCreateRelationshipsTrait;
    use WithRemoveRelationshipsTrait;

    /**
     * @inheritdoc
     */
    protected function methodToAbilityMap(): array
    {
        return [
            'show' => AbilitiesInterface::SHOW_RESOURCE,
            'create' => AbilitiesInterface::CREATE_RESOURCE,
            'update' => AbilitiesInterface::UPDATE_RESOURCE,
            'remove' => AbilitiesInterface::REMOVE_RESOURCE,
            'list' => AbilitiesInterface::LIST_RESOURCES,

            'showRelated' => AbilitiesInterface::SHOW_RELATIONSHIPS,
            'showRelationships' => AbilitiesInterface::SHOW_RELATIONSHIPS,
            'createRelationships' => AbilitiesInterface::CREATE_RELATIONSHIPS,
            'updateRelationships' => AbilitiesInterface::UPDATE_RELATIONSHIPS,
            'removeRelationships' => AbilitiesInterface::REMOVE_RELATIONSHIPS,
        ];
    }
}
