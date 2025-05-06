<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

class UpdateResourceAction extends AbstractAction
{
    public function __construct(
        protected Request $request,
    ) {
    }

    public function authorize(): void
    {
        $this->gate()->authorize(AbilitiesInterface::UPDATE, $this->request->resource());
    }

    public function handle(): Response
    {
        $resource = $this->request->resource();
        $resource = $this->manipulator()->hydrateResource($resource, $this->request->getData());

        $this->em()->flush();

        return $this->response()->item($resource);
    }
}
