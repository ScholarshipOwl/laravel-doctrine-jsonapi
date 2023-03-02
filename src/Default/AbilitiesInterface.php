<?php

namespace Sowl\JsonApi\Default;

interface AbilitiesInterface
{
    const SHOW = 'show';
    const CREATE = 'create';
    const UPDATE = 'update';
    const REMOVE = 'remove';
    const LIST = 'list';


    const CREATE_RELATIONSHIPS = 'createRelationships';

    const UPDATE_RELATIONSHIPS = 'updateRelationships';

    const REMOVE_RELATIONSHIPS = 'removeRelationships';
}
