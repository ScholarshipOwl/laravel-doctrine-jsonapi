<?php

namespace Sowl\JsonApi\Fractal;

use League\Fractal\Manager;

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
     * It then processes the "include", "exclude", "fields", and "meta" parameters from the options DTO.
     */
    public function __construct(protected FractalOptions $options)
    {
        parent::__construct(new ScopeFactory());

        $serializer = new JsonApiSerializer($options->baseUrl);
        $this->setSerializer($serializer);

        if ($options->includes) {
            $this->parseIncludes($options->includes);
        }

        if ($options->excludes) {
            $this->parseExcludes($options->excludes);
        }

        if ($options->fields) {
            $this->parseFieldsets($options->fields);
        }

        if ($options->meta) {
            $this->parseMetasets($options->meta);
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

            // Remove empty and repeated fields
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
