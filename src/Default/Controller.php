<?php

namespace Sowl\JsonApi\Default;

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