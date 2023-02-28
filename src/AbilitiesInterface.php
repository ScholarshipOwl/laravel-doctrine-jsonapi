<?php

namespace Sowl\JsonApi;

interface AbilitiesInterface
{
    const SHOW_RESOURCE = 'show';
    const CREATE_RESOURCE = 'create';
    const UPDATE_RESOURCE = 'update';
    const REMOVE_RESOURCE = 'remove';
    const LIST_RESOURCES = 'list';

    const SHOW_RELATIONSHIPS = 'showRelationships';

    const CREATE_RELATIONSHIPS = 'createRelationships';

    const UPDATE_RELATIONSHIPS = 'updateRelationships';

    const REMOVE_RELATIONSHIPS = 'removeRelationships';
}
