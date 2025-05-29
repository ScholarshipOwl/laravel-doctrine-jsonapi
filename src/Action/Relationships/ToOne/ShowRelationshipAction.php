<?php

namespace Sowl\JsonApi\Action\Relationships\ToOne;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

class ShowRelationshipAction extends AbstractAction
{
    public function __construct(
        protected ToOneRelationship $relationship,
        protected Request $request,
    ) {
    }

    public function authorize(): void
    {
        $ability = $this->authAbility();
        $this->gate()->authorize($ability, [$this->request->resource()]);
    }

    public function authAbility(): string
    {
        return AbilitiesInterface::VIEW . $this->relationship->pascalCaseName();
    }

    public function handle(): Response
    {
        $resource = $this->request->resource();
        $field = $this->relationship->property();

        if ($relation = $this->manipulator()->getProperty($resource, $field)) {
            return $this->response()->item($relation, relationship: true);
        }

        return $this->response()->null();
    }
}
