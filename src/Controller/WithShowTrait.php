<?php

namespace Sowl\JsonApi\Controller;

use Sowl\JsonApi\Action\Resource\ShowResource;
use Sowl\JsonApi\Response;
use Sowl\JsonApi\Request;

trait WithShowTrait
{
    public function show(Request $request): Response
    {
        return (new ShowResource())->dispatch($request);
    }
}
