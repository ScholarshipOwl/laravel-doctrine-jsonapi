<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\ListRelationshipsAction;
use Sowl\JsonApi\Action\Relationships\ToOne\ShowRelationshipAction;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

/**
 * Provides a showRelationships method for handling the retrieval of relationships between resources.
 *
 * By using the WithRelationshipTrait in your controller classes, you can quickly and easily add the functionality to
 * show relationships between resources in your JSON:API implementation. This trait helps to handle both to-one and
 * to-many relationships in a uniform way.
 */
trait WithRelationshipTrait
{
    /**
     * Handles "GET /{resourceKey}/{id}/relationships/{relationship}" route.
     *
     * Based on the type of relationship, the method creates a new instance of the ShowRelationshipAction class for
     * a ToOneRelationship or a new instance of the ListRelationshipsAction class for a ToManyRelationship.
     *
     * If the relationship is not found the method returns a "Not Found" response.
     */
    public function showRelationships(Request $request): Response
    {
        $resource = $request->resource();
        $relationshipName = $request->relationshipName();
        $relationship = $resource->relationships()->get($relationshipName);

        if ($relationship instanceof ToOneRelationship) {
            return (new ShowRelationshipAction($relationship))
                ->dispatch($request);
        }

        if ($relationship instanceof ToManyRelationship) {
            return (new ListRelationshipsAction($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}
