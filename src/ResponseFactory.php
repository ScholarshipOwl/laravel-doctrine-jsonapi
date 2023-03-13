<?php

namespace Sowl\JsonApi;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Routing\Redirector;
use Sowl\JsonApi\Exceptions\JsonApiException;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Sowl\JsonApi\Fractal\Fractal;
use Sowl\JsonApi\Fractal\RelationshipsTransformer;

class ResponseFactory extends \Illuminate\Routing\ResponseFactory
{
    public function request(): ?Request
    {
        return app('request.jsonapi');
    }

    public function jsonapi(?array $body, int $status = Response::HTTP_OK, array $header = []): Response
    {
        return new Response($body, $status, $header);
    }

    public function item(
        ResourceInterface   $resource,
        int                 $status = Response::HTTP_OK,
        array               $headers = [],
        array               $meta = [],
        bool                $relationship = false,
    ): Response
    {
        $transformer = $resource->transformer();

        if ($relationship) {
            $transformer = new RelationshipsTransformer($transformer);
        }

        $item = (new Item($resource, $transformer, $resource->getResourceType()));

        if (!empty($meta)) {
            $item->setMeta($meta);
        }

        $body = $this->fractal()->createData($item)->toArray();
        return $this->jsonapi($body, $status, $headers);
    }

    public function created(
        ResourceInterface   $resource,
        array               $headers = [],
        array               $meta = []
    ): Response
    {
        return $this->item(
            resource: $resource,
            status: Response::HTTP_CREATED,
            headers: array_merge($headers, [
                'Location' => $this->linkToResource($resource),
            ]),
            meta: $meta
        );
    }

    public function collection(
        array|DoctrineCollection $collection,
        string $resourceType,
        AbstractTransformer $transformer,
        int                 $status = Response::HTTP_OK,
        array               $headers = [],
        array               $meta = [],
        bool                $relationship = false,
    ): Response
    {
        if ($relationship) {
            $transformer = new RelationshipsTransformer($transformer);
        }

        $collection = (new Collection($collection, $transformer, $resourceType));

        if (!empty($meta)) {
            $collection->setMeta($meta);
        }

        $body = $this->fractal()->createData($collection)->toArray();
        return $this->jsonapi($body, $status, $headers);
    }

    public function query(
        QueryBuilder        $qb,
        string              $resourceType,
        AbstractTransformer $transformer,
        int                 $status = Response::HTTP_OK,
        array               $headers = [],
        array               $meta = [],
        bool                $relationship = false,
    ): Response
    {
        $data = new Paginator($qb, false);

        if ($relationship) {
            $transformer = new RelationshipsTransformer($transformer);
        }

        $collection = (new Collection($data, $transformer, $resourceType));

        if (!empty($meta)) {
            $collection->setMeta($meta);
        }

        if ($qb->getMaxResults()) {
            $size = $qb->getMaxResults();
            $basePath = $this->request()->getBasePath();

            $collection->setPaginator(new DoctrinePaginatorAdapter($data,
                fn (int $page) => $basePath.'?'.http_build_query(['page' => ['number' => $page, 'size' => $size]]),
            ));
        }

        $body = $this->fractal()->createData($collection)->toArray();
        return $this->jsonapi($body, $status, $headers);
    }

    public function null(int $status = Response::HTTP_OK, array $headers = []): Response
    {
        return $this->jsonapi(['data' => null], $status, $headers);
    }

    public function exception(JsonApiException $e): Response
    {
        return $this->jsonapi(['errors' => $e->errors()], $e->getCode());
    }

    public function noContent($status = Response::HTTP_NO_CONTENT, array $headers = []): Response
    {
        return $this->jsonapi(null, $status, $headers);
    }

    public function notFound($status = Response::HTTP_NOT_FOUND, array $headers = []): Response
    {
        return $this->jsonapi(null, $status, $headers);
    }

    protected function linkToResource(ResourceInterface $resource): string
    {
        return sprintf('%s/%s/%s', $this->request()->getBaseUrl(), $resource->getResourceType(), $resource->getId());
    }

    protected function fractal(): Fractal
    {
        return new Fractal($this->request());
    }
}
