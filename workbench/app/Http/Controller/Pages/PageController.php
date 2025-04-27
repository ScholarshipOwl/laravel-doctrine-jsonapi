<?php

namespace App\Http\Controller\Pages;

use Sowl\JsonApi\Action\Relationships\ToOne\UpdateRelationshipAction;
use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Default\WithShowTrait;
use Sowl\JsonApi\Response;

class PageController extends Controller
{
    use WithShowTrait;

    public function updateUserRelationship(UpdatePageUserRequest $request): Response
    {
        return (new UpdateRelationshipAction($request->relationship()))
            ->dispatch($request);
    }

    protected function noAuthMethods(): array
    {
        return [
            'show',
        ];
    }
}
