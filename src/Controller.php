<?php

namespace Sowl\JsonApi;

use Sowl\JsonApi\Controller\AuthorizesRequestsTrait;
use Sowl\JsonApi\Default\AbilitiesInterface;

class Controller extends \Illuminate\Routing\Controller
{
    use AuthorizesRequestsTrait;

    public function __construct()
    {
        $this->authorizeResource();
    }

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