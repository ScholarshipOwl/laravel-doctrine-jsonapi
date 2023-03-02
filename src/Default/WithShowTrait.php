<?php

namespace Sowl\JsonApi\Default;

use Sowl\JsonApi\Action\Resource\ShowResourceAction;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Request;

trait WithShowTrait
{
    public function show(Request $request): Response
    {
        return (new ShowResourceAction())
            ->dispatch($request);
    }
}
