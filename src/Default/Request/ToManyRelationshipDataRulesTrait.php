<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\Exceptions\NotFoundException;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;

/**
 * Trait that provides a method to define common validation rules for handling relationships.
 * Trait can be used by request classes that deal with relationships, such as creating, updating, or removing them.
 *
 * By using the ToManyRelationshipDataRulesTrait trait, you can ensure that the incoming request data for handling
 * relationships is validated according to the relationship object identifier rule.
 * This trait can be easily reused in different request classes that require similar validation rules for relationships.
 */
trait ToManyRelationshipDataRulesTrait
{
    abstract public function relationship(): ToOneRelationship|ToManyRelationship;

    /**
     * Verifies that we handle To-Many relationship request.
     * If the relationship is a To-One relationship, the function throws a NotFoundException, indicating that the
     * requested action is not available for the user.
     */
    protected function prepareForValidation(): void
    {
        $relationship = $this->relationship();
        if (!$relationship instanceof ToManyRelationship) {
            throw NotFoundException::create('Relationship is not of to-many type.')
                ->detail(sprintf(
                    'Requested relationship "%s" is not of to-many type.',
                    $relationship->name()
                ));
        }
    }

    /**
     * Method returns an array of validation rules for the request data.
     * These rules are used to validate the incoming to-many relationships request data before processing it.
     *
     * The rules defined in this method are:
     *   'data' => 'required|array': This rule enforces data be an array.
     *   'data.*' => [$this->relationship()->objectIdentifierRule()]': This rule enforces that each element in the data
     *                                                                 array must follow the object identifier rule
     *                                                                 provided by the relationship object.
     */
    public function dataRules(): array
    {
        return [
            'data' => 'required|array',
            'data.*' => [$this->relationship()->objectIdentifierRule()]
        ];
    }
}
