<?php

namespace Sowl\JsonApi\Fractal;

/**
 * Class extends the League\Fractal\Serializer\JsonApiSerializer class and provides a custom implementation
 * of the item() method to remove attributes from the serialized data when the
 * RelationshipsTransformer::ATTRIBUTE_RELATIONSHIPS key is present.
 *
 * By using this custom JsonApiSerializer, you can ensure that attributes are removed from the serialized data when
 * the RelationshipsTransformer::ATTRIBUTE_RELATIONSHIPS key is present, which is useful for
 * handling /resource/relationships/* actions according to the JSON API specification.
 */
class JsonApiSerializer extends \League\Fractal\Serializer\JsonApiSerializer
{
    /**
     * It calls the item() method of the parent JsonApiSerializer and then checks
     * if the RelationshipsTransformer::ATTRIBUTE_RELATIONSHIPS key is set in the $data['attributes'] array.
     * If it is, it removes the entire attributes array from the serialized data.
     * Finally, the modified serialized data is returned.
     */
    public function item($resourceKey, array $data, bool $includeAttributes = true): array
    {
        $item = parent::item($resourceKey, $data);

        if ($item['data']['attributes'][RelationshipsTransformer::ATTRIBUTE_RELATIONSHIPS] ?? false) {
            unset($item['data']['attributes']);
        }

        return $item;
    }

    /**
     * {@inheritdoc}
     */
    public function injectAvailableIncludeData(array $data, array $availableIncludes): array
    {
        if (! $this->shouldIncludeLinks()) {
            return $data;
        }

        if ($this->isCollection($data)) {
            $data['data'] = array_map(function ($resource) use ($availableIncludes) {
                foreach ($availableIncludes as $relationshipKey) {
                    $resource = $this->addRelationshipLinks($resource, $relationshipKey);
                }

                return $resource;
            }, $data['data']);
        } else {
            foreach ($availableIncludes as $relationshipKey) {
                $data['data'] = $this->addRelationshipLinks($data['data'], $relationshipKey);
            }
        }

        return $data;
    }

    /**
     * Adds links for all available includes to a single resource.
     *
     * @param  array  $resource  The resource to add relationship links to
     * @param  string  $relationshipKey  The resource key of the relationship
     */
    private function addRelationshipLinks(array $resource, string $relationshipKey): array
    {
        if (isset($resource['relationships'][$relationshipKey]['data'])) {
            $resource['relationships'][$relationshipKey] = array_merge(
                [
                    'links' => [
                        'self' => "{$this->baseUrl}/{$resource['type']}/{$resource['id']}/relationships/{$relationshipKey}",
                        'related' => "{$this->baseUrl}/{$resource['type']}/{$resource['id']}/{$relationshipKey}",
                    ],
                ],
                $resource['relationships'][$relationshipKey]
            );
        }

        return $resource;
    }
}
