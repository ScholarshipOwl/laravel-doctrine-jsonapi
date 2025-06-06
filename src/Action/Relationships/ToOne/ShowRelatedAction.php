<?php

namespace Sowl\JsonApi\Action\Relationships\ToOne;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Exceptions\BadRequestException;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

class ShowRelatedAction extends AbstractAction
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

    /**
     * @throws BadRequestException
     */
    public function handle(): Response
    {
        $resource = $this->request->resource();
        $property = $this->relationship->property();

        if ($relation = $this->manipulator()->getProperty($resource, $property)) {
            return $this->response()->item($relation);
        }

        return $this->response()->null();
    }
}
