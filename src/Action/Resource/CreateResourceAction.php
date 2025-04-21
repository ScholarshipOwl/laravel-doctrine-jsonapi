<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Response;

class CreateResourceAction extends AbstractAction
{
    public function handle(): Response
    {
        $data = $this->request()->getData();
        $resource = $this->manipulator()->createResource(
            $data['type'] ?? $this->repository()->getResourceType(),
            $data['id'] ?? null
        );

        $this->manipulator()->hydrateResource($resource, $data);

        $this->em()->persist($resource);
        $this->em()->flush();

        return $this->response()->created($resource);
    }
}
