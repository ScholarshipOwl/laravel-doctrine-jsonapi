<?php

namespace Sowl\JsonApi;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Routing\Redirector;
use Sowl\JsonApi\Exceptions\JsonApiException;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use League\Fractal\Manager as Fractal;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Sowl\JsonApi\Fractal\JsonApiSerializer;
use Sowl\JsonApi\Fractal\ScopeFactory;

class ResponseFactory extends \Illuminate\Routing\ResponseFactory
{
    public function __construct(
        ViewFactory $view,
        Redirector $redirector,
        protected AbstractRequest $request
    ) {
        parent::__construct($view, $redirector);
    }

    public function request(): AbstractRequest
    {
        return $this->request;
    }

    public function jsonapi(?array $body, int $status = JsonApiResponse::HTTP_OK, array $header = []): JsonApiResponse
    {
        return new JsonApiResponse($body, $status, $header);
    }

    public function item(
        ResourceInterface   $resource,
        int                 $status = JsonApiResponse::HTTP_OK,
        array               $headers = [],
        array               $meta = [],
        bool                $relationship = false,
    ): JsonApiResponse
    {
        $transformer = $resource->transformer();

        if ($relationship) {
            $transformer = new RelationshipsTransformer($transformer);
        }

        $item = (new Item($resource, $transformer, $resource->getResourceKey()))->setMeta($meta);
        $body = $this->fractal()->createData($item)->toArray();
        return $this->jsonapi($body, $status, $headers);
    }

    public function created(
        ResourceInterface   $resource,
        array               $headers = [],
        array               $meta = []
    ): JsonApiResponse
    {
        return $this->item(
            resource: $resource,
            status: JsonApiResponse::HTTP_CREATED,
            headers: array_merge($headers, [
                'Location' => $this->linkToResource($resource),
            ]),
            meta: $meta
        );
    }

    public function collection(
        array|DoctrineCollection $collection,
        string $resourceKey,
        AbstractTransformer $transformer,
        int                 $status = JsonApiResponse::HTTP_OK,
        array               $headers = [],
        bool                $relationship = false,
    ): JsonApiResponse
    {
        if ($relationship) {
            $transformer = new RelationshipsTransformer($transformer);
        }

        $collection = (new Collection($collection, $transformer, $resourceKey));
        $body = $this->fractal()->createData($collection)->toArray();
        return $this->jsonapi($body, $status, $headers);
    }

    public function query(
        QueryBuilder        $qb,
        ResourceRepository  $repository,
        int                 $status = JsonApiResponse::HTTP_OK,
        array               $headers = [],
        array               $meta = [],
        bool                $relationship = false,
    ): JsonApiResponse
    {
        $data = new Paginator($qb, false);
        $resourceKey = $repository->getResourceKey();
        $transformer = $repository->transformer();

        if ($relationship) {
            $transformer = new RelationshipsTransformer($transformer);
        }

        $collection = (new Collection($data, $transformer, $resourceKey))->setMeta($meta);

        if ($qb->getMaxResults()) {
            $collection->setPaginator(
                new DoctrinePaginatorAdapter(
                    $data,
                    function(int $page) {
                        // return !$resourceKey ? null : "{$request->getBaseUrl()}/$resourceKey?".http_build_query([
                        return $this->request()->getBasePath().'?'.http_build_query([
                                'page' => [
                                    'number'    => $page,
                                    'size'      => $this->request()->getMaxResults()
                                ]
                            ]);
                    }
                )
            );
        }

        $body = $this->fractal()->createData($collection)->toArray();
        return $this->jsonapi($body, $status, $headers);
    }

    public function null(int $status = JsonApiResponse::HTTP_OK, array $headers = []): JsonApiResponse
    {
        return $this->jsonapi(['data' => null], $status, $headers);
    }

    public function exception(JsonApiException $e): JsonApiResponse
    {
        return $this->jsonapi(['errors' => $e->errors()], $e->getCode());
    }

    public function noContent($status = JsonApiResponse::HTTP_NO_CONTENT, array $headers = []): JsonApiResponse
    {
        return $this->jsonapi(null, $status, $headers);
    }

    protected function linkToResource(ResourceInterface $resource): string
    {
        return sprintf('%s/%s/%s', $this->request()->getBaseUrl(), $resource->getResourceKey(), $resource->getId());
    }

    protected function fractal(): Fractal
    {
        $request = $this->request();
        $serializer = new JsonApiSerializer($request);
        $scopeFactory = new ScopeFactory($request);

        $fractal = new Fractal($scopeFactory);
        $fractal->setSerializer($serializer);

        if ($includes = $request->getInclude()) {
            $fractal->parseIncludes($includes);
        }

        if ($excludes = $request->getExclude()) {
            $fractal->parseExcludes($excludes);
        }

        if ($fields = $request->getFields()) {
            $fractal->parseFieldsets($fields);
        }

        return $fractal;
    }
}
