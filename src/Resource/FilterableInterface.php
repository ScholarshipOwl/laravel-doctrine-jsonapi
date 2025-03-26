<?php

namespace Sowl\JsonApi\Resource;

use Sowl\JsonApi\FilterParsers\AbstractFilterParser;
use Sowl\JsonApi\Request;

interface FilterableInterface
{
    /**
     * @return AbstractFilterParser[]
     */
    public static function filterParsers(Request $request): array;
}
