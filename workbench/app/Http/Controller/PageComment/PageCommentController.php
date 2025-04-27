<?php

namespace App\Http\Controller\PageComment;

use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Default\WithRelatedTrait;
use Sowl\JsonApi\Default\WithRelationshipTrait;
use Sowl\JsonApi\Default\WithShowTrait;

class PageCommentController extends Controller
{
    use WithRelatedTrait;
    use WithRelationshipTrait;
    use WithShowTrait;

    protected function noAuthMethods(): array
    {
        return [
            'show',

            // This is added for test purposes to properly check not found functionality of the trait.
            'showRelated',
            'showRelationships',
        ];
    }
}
