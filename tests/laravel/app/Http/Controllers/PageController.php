<?php

namespace Tests\App\Http\Controllers;

use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Action\Relationships\ToOne\UpdateRelationshipAction;
use Sowl\JsonApi\Default\WithShowTrait;
use Sowl\JsonApi\Response;
use Tests\App\Actions\Page\UpdateUserRelationshipsRequest;

class PageController extends Controller
{
    use WithShowTrait;

    public function updateUserRelationship(UpdateUserRelationshipsRequest $request): Response
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
