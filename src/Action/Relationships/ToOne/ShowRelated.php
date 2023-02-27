<?php

namespace Sowl\JsonApi\Action\Relationships\ToOne;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Exceptions\BadRequestException;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Response;

class ShowRelated extends AbstractAction
{
    public function __construct(protected ToOneRelationship $relationship) {}

    /**
     * @throws BadRequestException
     */
    public function handle(): Response
	{
        $resource = $this->request()->resource();
        $field = $this->relationship->field();

        if ($relation = $this->manipulator()->getProperty($resource, $field)) {
            return response()->item($relation);
        }

        return response()->null();
	}
}
