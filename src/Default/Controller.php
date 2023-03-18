<?php

namespace Sowl\JsonApi\Default;

/**
 * Class is a default implementation of a JSON:API controller.
 *
 * It extends the base \Sowl\JsonApi\Controller class and includes several traits for common
 * CRUD (Create, Read, Update, Delete) operations and relationship management.
 *
 * Controller class can handle most common JSON:API operations and relationship management tasks.
 * You can extend this class and override methods as needed to customize the behavior of your JSON:API controller.
 */
final class Controller extends \Sowl\JsonApi\Controller
{
    use WithShowTrait;
    use WithCreateTrait;
    use WithUpdateTrait;
    use WithRemoveTrait;
    use WithListTrait;

    use WithRelatedTrait;
    use WithRelationshipTrait;
    use WithUpdateRelationshipsTrait;
    use WithCreateRelationshipsTrait;
    use WithRemoveRelationshipsTrait;
}