<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

class CreateResourceAction extends AbstractAction
{
    public function __construct(
        protected Request $request,
    ) {}

    public function authorize(): void
    {
        $this->gate()->authorize(AbilitiesInterface::CREATE, $this->request->repository()->getClassName());
    }

    public function handle(): Response
    {
        $data = $this->request->getData();
        $resource = $this->manipulator()->createResource(
            $data['type'] ?? $this->request->repository()->getResourceType(),
            $data['id'] ?? null
        );

        $this->manipulator()->hydrateResource($resource, $data);

        $this->em()->persist($resource);
        $this->em()->flush();

        return $this->response()->created($resource);
    }
}
