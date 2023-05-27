<?php

namespace Sowl\JsonApi\Default\Resource;

use Sowl\JsonApi\FilterParsers\ArrayFilterParser;
use Sowl\JsonApi\FilterParsers\SearchFilterParser;
use Sowl\JsonApi\Request;

trait WithFilterParsersTrait
{
    abstract static public function searchProperty(): ?string;
    abstract static public function filterable(): array;

    public static function filterParsers(Request $request): array
    {
        return [
            new SearchFilterParser($request, static::searchProperty()),
            new ArrayFilterParser($request, static::filterable())
        ];
    }
}