<?php namespace Sowl\JsonApi\Controller;

use Illuminate\Routing\Controller;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Action\Resource\CreateResource;
use Sowl\JsonApi\Action\Resource\RemoveResource;
use Sowl\JsonApi\Action\Resource\ListResources;
use Sowl\JsonApi\Action\Resource\ShowResource;
use Sowl\JsonApi\Action\Resource\UpdateResource;
use Sowl\JsonApi\JsonApiRequest;
use Sowl\JsonApi\JsonApiResponse;
use Sowl\JsonApi\ResourceRepository;

abstract class AbstractController extends Controller
{
    abstract protected function transformer(): AbstractTransformer;
    abstract protected function repository(): ResourceRepository;

    /**
     * Param that can be filtered if query is string.
     */
    protected function getFilterProperty(): ?string
    {
        return null;
    }

    /**
     * Get list of filterable entity properties.
     */
    protected function getFilterable(): array
    {
        return [];
    }

    public function index(JsonApiRequest $request): JsonApiResponse
    {
        return (new ListResources($this->repository(), $this->transformer()))
            ->setSearchProperty($this->getFilterProperty())
            ->setFilterable($this->getFilterable())
            ->dispatch($request);
    }

    public function create(JsonApiRequest $request): JsonApiResponse
    {
        return (new CreateResource($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function show(JsonApiRequest $request): JsonApiResponse
    {
        return (new ShowResource($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function update(JsonApiRequest $request): JsonApiResponse
    {
        return (new UpdateResource($this->repository(), $this->transformer()))->dispatch($request);
    }

    public function delete(JsonApiRequest $request): JsonApiResponse
    {
        return (new RemoveResource($this->repository(), $this->transformer()))->dispatch($request);
    }
}
