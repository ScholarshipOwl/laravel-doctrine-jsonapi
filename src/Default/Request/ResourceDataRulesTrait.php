<?php

namespace Sowl\JsonApi\Default\Request;

use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\ResourceRepository;

/**
 * Trait provides a default implementation of the dataRules() method for request classes.
 *
 * This method returns an array of validation rules for request data, which are used to validate the incoming request
 * data before processing it. This trait is designed to be used in classes that extend Sowl\JsonApi\Request.
 *
 * By using the DefaultResourceDataRulesTrait, you can ensure that the incoming request data is validated according to
 * the attributes and relationships defined in your resource.
 */
trait ResourceDataRulesTrait
{
    abstract public function repository(): ResourceRepository;
    abstract public function rm(): ResourceManager;
    abstract public function resourceType(): string;

    /**
     * The dataRules() method provided by this trait combines rules for attributes and relationships of a resource.
     */
    public function dataRules(): array
    {
        return array_merge(
            ['data' => 'required|array'],
            $this->attributeRules(),
            $this->relationshipsRules(),
        );
    }

    /**
     * Method returns an array of validation rules for resource attributes.
     *
     * It iterates through the resource's fields from Doctrine metadata to get a list of attributes.
     * Using 'sometimes' rule used to make sure that the data will be included in the validated method output.
     */
    private function attributeRules(): array
    {
        $rules = [];

        $metadata = $this->repository()->metadata();
        foreach (array_keys($metadata->reflFields) as $attribute) {
            $rules["data.attributes.$attribute"] = ['sometimes'];
        }

        return $rules;
    }

    /**
     * Method returns an array of validation rules for resource relationships.
     *
     * It iterates through the resource's relationships and adds a rule for each relationship based on the
     * object identifier rule provided by the relationship object.
     */
    private function relationshipsRules(): array
    {
        $rules = [];

        $relationships = $this->rm()->relationshipsByresourceType($this->resourceType())->all();
        foreach ($relationships as $name => $relationship) {
            $rules["data.relationships.$name.data"] = [$relationship->objectIdentifierRule()];
        }

        return $rules;
    }
}
