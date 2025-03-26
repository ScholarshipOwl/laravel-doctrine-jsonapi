<?php

namespace Sowl\JsonApi\Scribe;

class DisplayHelper
{
    public static function displayResourceType(string $resourceType): string
    {
        $display = \Str::headline(\Str::singular(\Str::snake($resourceType)));
        $display = ucfirst(strtolower($display));

        return $display;
    }
}
