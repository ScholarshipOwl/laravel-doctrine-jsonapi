<?php

namespace Sowl\JsonApi\Default;

final class Controller extends \Sowl\JsonApi\Controller
{
    use WithCreateTrait;
    use WithListTrait;
    use WithRemoveTrait;
    use WithShowTrait;
    use WithUpdateTrait;

    use WithCreateRelationshipsTrait;
    use WithRelatedTrait;
    use WithRelationshipTrait;
    use WithRemoveRelationshipsTrait;
    use WithUpdateRelationshipsTrait;
}
