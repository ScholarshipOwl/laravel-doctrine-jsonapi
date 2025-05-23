<?php

namespace App\Http\Controller\PageComment;

use Illuminate\Routing\Controller;
use Sowl\JsonApi\Action\Resource\ShowResourceAction;
use Sowl\JsonApi\Default\WithRelatedTrait;
use Sowl\JsonApi\Default\WithRelationshipTrait;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Scribe\Attributes\ResourceRequest;
use Sowl\JsonApi\Scribe\Attributes\ResourceResponse;

class PageCommentController extends Controller
{
    use WithRelatedTrait;
    use WithRelationshipTrait;

    #[ResourceRequest]
    #[ResourceResponse]
    public function show(Request $request): Response
    {
        return (new ShowResourceAction($request))->disableAuthorization()->dispatch();
    }
}
