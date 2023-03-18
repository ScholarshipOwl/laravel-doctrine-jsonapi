<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\ListRelatedAction;
use Sowl\JsonApi\Action\Relationships\ToOne\ShowRelatedAction;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

/**
 * Provides a showRelated method for handling the retrieval of related resources.
 *
 * By using the WithRelatedTrait in your controller classes, you can quickly and easily add the functionality to show
 * related resources in your JSON:API implementation. This trait helps to handle both to-one and to-many relationships
 * in a uniform way.
 */
trait WithRelatedTrait
{
    /**
     * Handles "GET /{resourceKey}/{id}/{relationship}" route.
     *
     * Based on the type of relationship, the method creates a new instance of the ShowRelatedAction class for
     * a ToOneRelationship or a new instance of the ListRelatedAction class for a ToManyRelationship.
     *
     * If the relationship is not found the method returns a "Not Found" response.
     */
    public function showRelated(Request $request): Response
    {
        $resource = $request->resource();
        $relationshipName = $request->relationshipName();
        $relationship = $resource->relationships()->get($relationshipName);

        if ($relationship instanceof ToOneRelationship) {
            return (new ShowRelatedAction($relationship))
                ->dispatch($request);
        }

        if ($relationship instanceof ToManyRelationship) {
            return (new ListRelatedAction($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}
