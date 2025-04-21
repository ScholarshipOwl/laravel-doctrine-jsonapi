<?php

namespace Sowl\JsonApi\Action\Relationships\ToOne;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Exceptions\BadRequestException;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Response;

class ShowRelatedAction extends AbstractAction
{
    public function __construct(protected ToOneRelationship $relationship)
    {
    }

    /**
     * @throws BadRequestException
     */
    public function handle(): Response
    {
        $resource = $this->request()->resource();
        $property = $this->relationship->property();

        if ($relation = $this->manipulator()->getProperty($resource, $property)) {
            return $this->response()->item($relation);
        }

        return $this->response()->null();
    }
}
