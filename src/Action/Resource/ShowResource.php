<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\JsonApiResponse;

class ShowResource extends AbstractAction
{
    public function handle(): JsonApiResponse
    {
        return response()->item($this->request()->resource());
    }
}
