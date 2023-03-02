<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\CreateRelationshipsAction;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Default\Request\DefaultCreateRelationshipsRequest;
use Sowl\JsonApi\Response;

trait WithCreateRelationshipsTrait
{
    public function createRelationships(DefaultCreateRelationshipsRequest $request): Response
    {
        $relationship = $request->relationship();

        if ($relationship instanceof ToManyRelationship) {
            return (new CreateRelationshipsAction($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}
