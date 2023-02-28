<?php

namespace Sowl\JsonApi\Controller;

use Sowl\JsonApi\Action\Relationships\ToMany\CreateRelationships;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Request\Relationships\ToMany\CreateRelationshipsRequest;
use Sowl\JsonApi\Response;

trait WithCreateRelationshipsTrait
{
    public function createRelationships(CreateRelationshipsRequest $request): Response
    {
        $relationship = $request->relationship();

        if ($relationship instanceof ToManyRelationship) {
            return (new CreateRelationships($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}