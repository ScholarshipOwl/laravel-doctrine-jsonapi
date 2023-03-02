<?php

namespace Tests\App\Http\Controller;

use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Action\Relationships\ToOne\UpdateRelationshipAction;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Default\WithShowTrait;
use Sowl\JsonApi\Response;
use Tests\App\Actions\Page\UpdateUserRelationshipsRequest;
use Tests\App\Entities\Page;

class PageController extends Controller
{
    use WithShowTrait;

    public function updateUserRelationship(UpdateUserRelationshipsRequest $request): Response
    {
        return (new UpdateRelationshipAction(Page::relationships()->toOne()->get('user')))
            ->dispatch($request);
    }

    protected function methodToAbilityMap(): array
    {
        return array_merge(parent::methodToAbilityMap(), [
            'show' => null,
            'updateUserRelationship' => [AbilitiesInterface::UPDATE_RELATIONSHIPS, 'user'],
        ]);
    }
}
