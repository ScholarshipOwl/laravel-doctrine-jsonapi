<?php

namespace Sowl\JsonApi\Action\Resource;

use Sowl\JsonApi\AbstractAction;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Response;

class ShowResourceAction extends AbstractAction
{
    public function __construct(
        protected Request $request,
    ) {
    }

    public function authorize(): void
    {
        $this->gate()->authorize(AbilitiesInterface::VIEW, $this->request->resource());
    }

    public function handle(): Response
    {
        $resource = $this->request->resource();

        return $this->response()->item($resource);
    }
}
