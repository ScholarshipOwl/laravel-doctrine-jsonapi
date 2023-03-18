<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\Request;

/**
 * Class used for handling update to-one relationship requests.
 *
 * This class ensures that the request data for updating a relationship between resources is properly validated
 * before processing.
 */
final class DefaultUpdateRelationshipRequest extends Request
{
    /**
     * Sets up the validation rules for the request data for updating relationships between resources.
     * It uses the relationship() method to get the relationship metadata and calls the objectIdentifierRule() method
     * on it to retrieve the validation rule for the relationship.
     */
    public function dataRules(): array
    {
        return [
            'data' => [$this->relationship()->objectIdentifierRule()]
        ];
    }
}