<?php

namespace Sowl\JsonApi\Default\Resource;

use Sowl\JsonApi\FilterParsers\ArrayFilterParser;
use Sowl\JsonApi\FilterParsers\SearchFilterParser;
use Sowl\JsonApi\Request;

trait WithFilterParsersTrait
{
    abstract public static function searchProperty(): ?string;

    abstract public static function filterable(): array;

    public static function filterParsers(Request $request): array
    {
        return [
            new SearchFilterParser($request, static::searchProperty()),
            new ArrayFilterParser($request, static::filterable()),
        ];
    }
}
