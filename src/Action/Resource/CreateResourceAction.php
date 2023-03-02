<?php namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Response;

class CreateResourceAction extends AbstractAction
{
    public function handle(): Response
    {
        $data = $this->request()->getData();
        $class = $this->repository()->getClassName();
        $resource = $this->manipulator()->hydrateResource(new $class, $data);

        $this->em()->persist($resource);
        $this->em()->flush();

        return response()->created($resource);
    }
}
