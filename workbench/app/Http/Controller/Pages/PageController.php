<?php

namespace App\Http\Controller\Pages;

use Illuminate\Routing\Controller;
use Sowl\JsonApi\Action\Relationships\ToOne\UpdateRelationshipAction;
use Sowl\JsonApi\Action\Resource\ShowResourceAction;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;

class PageController extends Controller
{
    #[ResourceRequest]
    #[ResourceResponse]
    public function show(ShowResourceAction $action): Response
    {
        return $action->disableAuthorization()->dispatch();
    }

    public function updateUserRelationship(UpdatePageUserRequest $request): Response
    {
        return (new UpdateRelationshipAction($request->relationship(), $request))
            ->dispatch();
    }
}
