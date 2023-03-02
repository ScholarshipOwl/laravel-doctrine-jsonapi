<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\ListResourcesAction;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

trait WithListTrait
{
    public function list(Request $request): Response
    {
        return (new ListResourcesAction())
            ->setSearchProperty($this->searchProperty())
            ->setFilterable($this->filterable())
            ->dispatch($request);
    }

    public function searchProperty(): ?string
    {
        return null;
    }

    public function filterable(): array
    {
        return [];
    }
}
