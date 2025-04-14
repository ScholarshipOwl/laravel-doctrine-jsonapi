<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\UpdateRelationshipsAction;
use Sowl\JsonApi\Action\Relationships\ToOne\UpdateRelationshipAction;
use Sowl\JsonApi\Default\Request\UpdateToManyRelationshipsRequest;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Scribe\Attributes\ResourceRelationshipsResponse;

/**
 * Provides an updateRelationships method for updating the relationships between resources.
 *
 * By using the WithUpdateRelationshipsTrait in your controller classes, you can quickly and easily add the
 * functionality to update relationships between resources in your JSON:API implementation. This trait helps to handle
 * both to-one and to-many relationships in a uniform way.
 *
 * Handles "PATCH /{resourceKey}/{id}/relationships/{relationship}" route.
 *
 *  Based on the type of relationship, the method creates a new instance of the UpdateRelationshipAction class for
 *  a ToOneRelationship or a new instance of the UpdateRelationshipsAction class for a ToManyRelationship.
 *
 *  If the relationship is not found the method returns a "Not Found" response.
 */
trait WithUpdateRelationshipsTrait
{
    #[ResourceRelationshipsResponse]
    public function updateRelationships(UpdateToManyRelationshipsRequest $request): Response
    {
        $relationship = $request->relationship();

        if ($relationship instanceof ToOneRelationship) {
            return (new UpdateRelationshipAction($relationship))
                ->dispatch($request);
        }

        if ($relationship instanceof ToManyRelationship) {
            return (new UpdateRelationshipsAction($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}
