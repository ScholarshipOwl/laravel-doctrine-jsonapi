<?php

namespace Sowl\JsonApi\Fractal;

use League\Fractal\ParamBag;
use Sowl\JsonApi\Request;
use League\Fractal\Manager;

class Fractal extends Manager
{
    protected array $requestedMetasets = [];

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

    public function getRequestedMetasets(): array
    {
        return $this->requestedMetasets;
    }

    public function getMetaset(string $type): ?array
    {
        return $this->requestedMetasets[$type] ?? null;
    }
}
