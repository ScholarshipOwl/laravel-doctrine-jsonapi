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
    public const VIEW = 'view';

    public const CREATE = 'create';

    public const UPDATE = 'update';

    public const DELETE = 'delete';

    public const LIST = 'viewAny';

    public const ATTACH = 'attach';

    public const DETACH = 'detach';
}
