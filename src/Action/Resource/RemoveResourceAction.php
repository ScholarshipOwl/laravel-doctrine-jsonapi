<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Response;

class RemoveResourceAction extends AbstractAction
{
    public function handle(): Response
    {
        $resource = $this->request()->resource();

        $this->em()->remove($resource);
        $this->em()->flush($resource);

        return $this->response()->noContent();
    }
}
