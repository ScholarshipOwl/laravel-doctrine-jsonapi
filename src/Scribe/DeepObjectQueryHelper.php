<?php

declare(strict_types=1);

namespace Sowl\JsonApi\Scribe;

class DeepObjectQueryHelper
{
    /**
     * Converts deepObject style query keys (e.g. fields[pageComments]) into PHP array format.
     *
     * @param array $query
     * @return array
     */
    public static function convert(array $query): array
    {
        foreach (array_keys($query) as $key) {
            $parts = explode('[', $key);
            if (count($parts) === 2) {
                $param = $parts[0];
                $deepKey = $parts[1] ? trim($parts[1], ']') : null;
                if ($param && $deepKey) {
                    $value = $query[$key];
                    unset($query[$key]);
                    $query[$param] = array_merge($query[$param] ?? [], [$deepKey => $value]);
                }
            }
        }
        return $query;
    }
}
