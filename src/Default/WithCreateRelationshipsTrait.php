<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Relationships\ToMany\CreateRelationshipsAction;
use Sowl\JsonApi\Default\Request\DefaultCreateRelationshipsRequest;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Response;

/**
 * Provides a createRelationships method for creating relationships between resources.
 *
 * By using the WithCreateRelationshipsTrait in your controller classes, you can quickly and easily add the
 * functionality to create relationships between resources in your JSON:API implementation. This trait helps to
 * handle to-many relationships.
 */
trait WithCreateRelationshipsTrait
{
    /**
     * Handles "POST /{resourceKey}/{id}/relationships/{relationship}" route.
     *
     * This trait is specifically designed to handle ToManyRelationships, as create for ToOne is not exists.
     * If the relationship is not found the method returns a "Not Found" response.
     */
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
