<?php

namespace Sowl\JsonApi\Fractal;

use League\Fractal\Manager;
use Sowl\JsonApi\Request;

/**
 * The Fractal class extends the League\Fractal\Manager class and provides a custom implementation for
 * handling JSON:API requests, specifically for parsing metasets.
 *
 * By using this custom Fractal class, you can process and manage JSON API requests more efficiently,
 * specifically in terms of handling metasets.
 */
class Fractal extends Manager
{
    protected array $requestedMetasets = [];

    /**
     * Sets up a new instance of ScopeFactory, creates a new JsonApiSerializer instance, and sets it as the serializer.
     * It then processes the "include", "exclude", "fields", and "meta" parameters from the request.
     */
    public function __construct(protected Request $request)
    {
        parent::__construct(new ScopeFactory());

        $serializer = new JsonApiSerializer($this->request->getBaseUrl());
        $this->setSerializer($serializer);

        if ($includes = $this->request->getInclude()) {
            $this->parseIncludes($includes);
        }

        if ($excludes = $this->request->getExclude()) {
            $this->parseExcludes($excludes);
        }

        if ($fields = $this->request->getFields()) {
            $this->parseFieldsets($fields);
        }

        if ($meta = $this->request->getMeta()) {
            $this->parseMetasets($meta);
        }
    }

    /**
     * It iterates through the metasets, converting the fields into arrays and removing empty and repeated fields.
     * The requestedMetasets array is then updated with the processed metasets.
     */
    public function parseMetasets(array $metasets): static
    {
        $this->requestedMetasets = [];

        foreach ($metasets as $type => $fields) {
            if (is_string($fields)) {
                $fields = explode(',', $fields);
            }

            //Remove empty and repeated fields
            $this->requestedMetasets[(string) $type] = array_unique(array_filter($fields));
        }

        return $this;
    }

    /**
     * This method returns the requestedMetasets array.
     */
    public function getRequestedMetasets(): array
    {
        return $this->requestedMetasets;
    }

    /**
     * This method accepts a string representing the type of metaset and returns the corresponding metaset
     * array if it exists, or null if it doesn't.
     */
    public function getMetaset(string $type): ?array
    {
        return $this->requestedMetasets[$type] ?? null;
    }
}
