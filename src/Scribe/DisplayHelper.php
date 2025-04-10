<?php

namespace Sowl\JsonApi\Scribe;

use Illuminate\Support\Str;

trait DisplayHelper
{
    protected function displayResourceType(?string $resourceType, bool $plural = true): ?string
    {
        if (is_null($resourceType)) {
            return null;
        }

        $display = Str::headline(Str::singular(Str::snake($resourceType)));
        $display = ucfirst(strtolower($display));

        return $plural ? Str::plural($display) : Str::singular($display);
    }
}
