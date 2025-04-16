<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\RemoveRelationshipsAction;
use Sowl\JsonApi\Default\Request\RemoveToManyRelationshipsRequest;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponseRelatinships;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;

/**
 * Provides a removeRelationships method for removing relationships between resources.
 *
 * By using the WithRemoveRelationshipsTrait in your controller classes, you can quickly and easily add the
 * functionality to remove relationships between resources in your JSON:API implementation. This trait helps to handle
 * to-many relationships.
 *
 * Handles "DELETE /{resourceKey}/{id}/relationships/{relationship}" route.
 *
 *  This trait is specifically designed to handle ToManyRelationships.
 *  If the relationship is not found the method returns a "Not Found" response.
 */
trait WithRemoveRelationshipsTrait
{
    #[ResourceRequest]
    #[ResourceResponseRelatinships(status: 204)]
    public function removeRelationships(RemoveToManyRelationshipsRequest $request): Response
    {
        $relationship = $request->relationship();

        if ($relationship instanceof ToManyRelationship) {
            return (new RemoveRelationshipsAction($relationship))
                ->dispatch($request);
        }

        return response()->notFound();
    }
}
