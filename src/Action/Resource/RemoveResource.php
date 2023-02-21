<?php namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\JsonApiResponse;

class RemoveResource extends AbstractAction
{
    public function handle(): JsonApiResponse
    {
        $resource = $this->request()->resource();

        $this->em()->remove($resource);
        $this->em()->flush($resource);

        return response()->noContent();
    }
}
