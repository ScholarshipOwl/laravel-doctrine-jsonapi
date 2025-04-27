<?php

namespace Sowl\JsonApi;

use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Routing\Redirector;
use League\Fractal\Pagination\DoctrinePaginatorAdapter;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use Sowl\JsonApi\Exceptions\JsonApiException;
use Sowl\JsonApi\Fractal\Fractal;
use Sowl\JsonApi\Fractal\FractalOptions;
use Sowl\JsonApi\Fractal\RelationshipsTransformer;

/**
 * "ResponseFactory" extends the Laravel response factory and provides methods for building JSON:API responses.
 * Used to simplify the serialization of Resource object or paginated queries into JSON:API responses.
 *
 * @link https://jsonapi.org/format/#fetching-resources-responses
 * @link https://jsonapi.org/format/#fetching-relationships
 */
class ResponseFactory extends \Illuminate\Routing\ResponseFactory
{
    public function __construct(ViewFactory $view, Redirector $redirector, protected ?Request $request = null)
    {
        parent::__construct($view, $redirector);
    }

    /**
     * Method takes a resource, status code, headers, meta, and a flag to indicate if it is a relationship response.
     * It transforms data with help of resource's transformer then creates a response from the transformed data and
     * provided HTTP status and headers.
     *
     * If relationship flag provided, the transformed data will have only object identifiers, without
     * "attributes" in the data.
     */
    public function item(
        ResourceInterface $resource,
        int $status = Response::HTTP_OK,
        array $headers = [],
        array $meta = [],
        bool $relationship = false,
    ): Response {
        $transformer = $resource->transformer();

        if ($relationship) {
            $transformer = new RelationshipsTransformer($transformer);
        }

        $item = (new Item($resource, $transformer, $resource->getResourceType()));

        if (! empty($meta)) {
            $item->setMeta($meta);
        }

        $body = $this->fractal()->createData($item)->toArray();

        return $this->buildResponse($body, $status, $headers);
    }

    /**
     * Method takes a resource, headers, and meta.
     * It calls the "item" method with the resource and a status code of 201 (Created), and adds a "Location" header
     * with the link to the newly created resource.
     *
     * @link https://jsonapi.org/format/#crud-creating-responses-201
     */
    public function created(
        ResourceInterface $resource,
        array $headers = [],
        array $meta = []
    ): Response {
        return $this->item(
            resource: $resource,
            status: Response::HTTP_CREATED,
            headers: array_merge($headers, [
                'Location' => $this->linkToResource($resource),
            ]),
            meta: $meta
        );
    }

    /**
     * Method takes a doctrine collection, resource type and transformer.
     * Collection transformed using the transformer creates a response from the transformed data and the provided
     * HTTP Status and headers.
     *
     * If relationship flag provided, the transformed data will have only object identifiers, without
     * "attributes" in the data.
     */
    public function collection(
        array|DoctrineCollection $collection,
        string $resourceType,
        AbstractTransformer $transformer,
        int $status = Response::HTTP_OK,
        array $headers = [],
        array $meta = [],
        bool $relationship = false,
    ): Response {
        if ($relationship) {
            $transformer = new RelationshipsTransformer($transformer);
        }

        $collection = (new Collection($collection, $transformer, $resourceType));

        if (! empty($meta)) {
            $collection->setMeta($meta);
        }

        $body = $this->fractal()->createData($collection)->toArray();

        return $this->buildResponse($body, $status, $headers);
    }

    /**
     * Method takes a query builder, resource type and transformer and builds JSON:API response.
     * It creates a Paginator from the query builder and transforms using provided transformer.
     * It then creates a response from the transformed data and the provided HTTP status and headers.
     *
     * If relationship flag provided, the transformed data will have only object identifiers, without
     * "attributes" in the data.
     */
    public function query(
        QueryBuilder $qb,
        string $resourceType,
        AbstractTransformer $transformer,
        int $status = Response::HTTP_OK,
        array $headers = [],
        array $meta = [],
        bool $relationship = false,
    ): Response {
        $data = new Paginator($qb, false);

        if ($relationship) {
            $transformer = new RelationshipsTransformer($transformer);
        }

        $collection = (new Collection($data, $transformer, $resourceType));

        if (! empty($meta)) {
            $collection->setMeta($meta);
        }

        if ($qb->getMaxResults()) {
            $size = $qb->getMaxResults();
            $basePath = $this->request()->getBasePath();

            $collection->setPaginator(new DoctrinePaginatorAdapter(
                $data,
                fn (int $page) => $basePath.'?'.http_build_query(['page' => ['number' => $page, 'size' => $size]]),
            ));
        }

        $body = $this->fractal()->createData($collection)->toArray();

        return $this->buildResponse($body, $status, $headers);
    }

    /**
     * Method takes a status code and headers and creates a response with a null "data".
     */
    public function null(int $status = Response::HTTP_OK, array $headers = []): Response
    {
        return $this->buildResponse(['data' => null], $status, $headers);
    }

    /**
     * Method takes a JsonApiException object and builds a response with the JSON:API errors generated by the exception.
     */
    public function exception(JsonApiException $e): Response
    {
        return $this->buildResponse(['errors' => $e->errors()], $e->getCode());
    }

    /**
     * Method returns No Body response with 204 status code.
     */
    public function emptyContent(int $status = Response::HTTP_NO_CONTENT, array $headers = []): Response
    {
        return $this->buildResponse(null, $status, $headers);
    }

    /**
     * Method returns Not Found response with 404 status code.
     */
    public function notFound(int $status = Response::HTTP_NOT_FOUND, array $headers = []): Response
    {
        return $this->buildResponse(null, $status, $headers);
    }

    /**
     * Method takes a resource and returns the URL for the resource.
     */
    protected function linkToResource(ResourceInterface $resource): string
    {
        return sprintf('%s/%s/%s', $this->request()->getBaseUrl(), $resource->getResourceType(), $resource->getId());
    }

    /**
     * Method takes a body, status code, and headers and creates a response with the provided parameters.
     */
    protected function buildResponse(?array $body, int $status = Response::HTTP_OK, array $headers = []): Response
    {
        return new Response($body, $status, $headers);
    }

    /**
     * Method returns a new Fractal object.
     */
    protected function fractal(): Fractal
    {
        return new Fractal(FractalOptions::fromRequest($this->request()));
    }

    /**
     * Method returns the JSON:API request object.
     */
    protected function request(): ?Request
    {
        // We must keep the app('request.jsonapi') because of tests implementation and how the request is created.
        return $this->request ?? app('request.jsonapi');
    }
}
