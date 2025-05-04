<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

class ShowResourceAction extends AbstractAction
{
    public function __construct(
        protected Request $request,
    ) {}

    public function handle(): Response
    {
        $resource = $this->request->resource();

        return $this->response()->item($resource);
    }
}
