<?php

namespace Sowl\JsonApi\Default;

/**
 * Interface that defines constants representing various CRUD (Create, Read, Update, Delete) operations and
 * relationship management abilities.
 *
 * These constants can be used to set up and manage permissions for different actions performed by controllers.
 */
interface AbilitiesInterface
{
    const VIEW = 'view';

    const CREATE = 'create';

    const UPDATE = 'update';

    const DELETE = 'delete';

    const LIST = 'viewAny';

    const ATTACH = 'attach';

    const DETACH = 'detach';
}
