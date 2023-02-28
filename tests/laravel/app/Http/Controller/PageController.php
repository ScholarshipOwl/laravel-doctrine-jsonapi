<?php

namespace Tests\App\Http\Controller;

use Sowl\JsonApi\Action\Relationships\ToOne\UpdateRelationship;
use Sowl\JsonApi\AbilitiesInterface;
use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Response;
use Tests\App\Actions\Page\UpdateUserRelationshipsRequest;
use Tests\App\Entities\Page;

class PageController extends Controller
{
    public function updateUserRelationship(UpdateUserRelationshipsRequest $request): Response
    {
        return (new UpdateRelationship(Page::relationships()->toOne()->get('user')))
            ->dispatch($request);
    }

    protected function methodToAbilityMap(): array
    {
        return [
            'show' => null,
            'updateUserRelationship' => [AbilitiesInterface::UPDATE_RELATIONSHIPS, 'user'],
        ] + parent::methodToAbilityMap();
    }
}
