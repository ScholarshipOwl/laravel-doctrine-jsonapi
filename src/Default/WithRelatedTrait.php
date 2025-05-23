<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\ListRelatedAction;
use Sowl\JsonApi\Action\Relationships\ToOne\ShowRelatedAction;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\ResponseFactory;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponseRelated;

/**
 * Provides a showRelated method for handling the retrieval of related resources.
 *
 * By using the WithRelatedTrait in your controller classes, you can quickly and easily add the functionality to show
 * related resources in your JSON:API implementation. This trait helps to handle both to-one and to-many relationships
 * in a uniform way.
 *
 * Handles "GET /{resourceKey}/{id}/{relationship}" route.
 *
 *  Based on the type of relationship, the method creates a new instance of the ShowRelatedAction class for
 *  a ToOneRelationship or a new instance of the ListRelatedAction class for a ToManyRelationship.
 *
 *  If the relationship is not found the method returns a "Not Found" response.
 */
trait WithRelatedTrait
{
    #[ResourceRequest]
    #[ResourceResponseRelated]
    public function showRelated(Request $request): Response
    {
        $relationship = $request->relationship();

        if ($relationship instanceof ToOneRelationship) {
            return ShowRelatedAction::makeDispatch($relationship, $request);
        }

        if ($relationship instanceof ToManyRelationship) {
            return ListRelatedAction::makeDispatch($relationship, $request);
        }

        return app(ResponseFactory::class)->notFound();
    }
}
