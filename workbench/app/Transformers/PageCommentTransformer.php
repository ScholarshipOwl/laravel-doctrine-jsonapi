<?php

namespace App\Transformers;

use App\Entities\Page;
use App\Entities\PageComment;
use App\Entities\User;
use League\Fractal\Resource\Item;
use Sowl\JsonApi\AbstractTransformer;

class PageCommentTransformer extends AbstractTransformer
{
    protected array $availableIncludes = [
        'user',
        'page',
    ];

    public function transform(PageComment $comment): array
    {
        return [
            'id' => $comment->getId(),
            'content' => $comment->getContent(),
        ];
    }

    public function includePages(PageComment $comment): Item
    {
        return $this->item($comment->getPage(), new PagesTransformer, Page::getResourceType());
    }

    public function includeUser(PageComment $comment): Item
    {
        return $this->item($comment->getUser(), new UserTransformer, User::getResourceType());
    }
}
