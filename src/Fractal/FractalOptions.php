<?php

declare(strict_types=1);

namespace Sowl\JsonApi\Fractal;

use Sowl\JsonApi\Request;

class FractalOptions
{
    public function __construct(
        public readonly ?array $includes = null,
        public readonly ?array $excludes = null,
        public readonly ?array $fields = null,
        public readonly ?array $meta = null,
        public readonly string $baseUrl = ''
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            includes: $request->getInclude(),
            excludes: $request->getExclude(),
            fields: $request->getFields(),
            meta: $request->getMeta(),
            baseUrl: $request->getBaseUrl()
        );
    }
}
