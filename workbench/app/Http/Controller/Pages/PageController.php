<?php

namespace App\Http\Controller\Pages;

use Illuminate\Routing\Controller;
use Sowl\JsonApi\Action\Relationships\ToOne\UpdateRelationshipAction;
use Sowl\JsonApi\Action\Resource\ShowResourceAction;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;

class PageController extends Controller
{
    #[ResourceRequest]
    #[ResourceResponse]
    public function show(Request $request): Response
    {
        return (new ShowResourceAction($request))->disableAuthorization()->dispatch();
    }

    public function updateUserRelationship(UpdatePageUserRequest $request): Response
    {
        return (new UpdateRelationshipAction($request->relationship(), $request))
            ->dispatch();
    }
}
