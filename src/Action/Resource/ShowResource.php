<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Response;

class ShowResource extends AbstractAction
{
    public function handle(): Response
    {
        return response()->item($this->request()->resource());
    }
}
