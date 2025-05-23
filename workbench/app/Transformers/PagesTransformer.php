<?php

namespace App\Transformers;

use App\Entities\Page;
use App\Entities\User;
use League\Fractal\Resource\Item;
use Sowl\JsonApi\AbstractTransformer;

class PagesTransformer extends AbstractTransformer
{
    protected array $availableIncludes = [
        'user',
    ];

    public function transform(Page $page): array
    {
        return [
            'id' => $page->getId(),
            'title' => $page->getTitle(),
            'content' => $page->getContent(),
        ];
    }

    public function includeUser(Page $page): Item
    {
        return $this->item($page->getUser(), new UserTransformer(), User::getResourceType());
    }
}
