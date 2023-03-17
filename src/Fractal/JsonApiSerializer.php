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
}
