<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Response;

class ShowResourceAction extends AbstractAction
{
    public function handle(): Response
    {
        $resource = $this->request()->resource();

        return response()->item($resource);
    }
}
