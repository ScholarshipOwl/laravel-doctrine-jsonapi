<?php

namespace Sowl\JsonApi\Default\Request;

use Doctrine\ORM\Mapping\FieldMapping;
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
     * Adds type-specific validation rules based on the field's Doctrine mapping information.
     */
    private function attributeRules(): array
    {
        $rules = [];

        $metadata = $this->repository()->metadata();
        $fieldMappings = $metadata->fieldMappings;
        $attributes = array_diff(array_keys($fieldMappings), $metadata->identifier);

        foreach ($attributes as $attribute) {
            $fieldMapping = $fieldMappings[$attribute];
            $fieldRules = ['sometimes'];

            // Add type-specific validation rules
            $typeRules = $this->getFieldTypeRules($fieldMapping);
            $fieldRules = array_merge($fieldRules, $typeRules);

            $rules["data.attributes.$attribute"] = $fieldRules;
        }

        return $rules;
    }

    /**
     * Generate validation rules based on field type and constraints from Doctrine metadata.
     *
     * @param  FieldMapping  $fieldMapping  The field mapping information from Doctrine metadata
     * @return array<string, mixed> List of Laravel validation rules for the field
     */
    private function getFieldTypeRules(FieldMapping $fieldMapping): array
    {
        $rules = [];

        // Add type-specific validation rules based on the field type
        if (isset($fieldMapping['type'])) {
            switch ($fieldMapping['type']) {
                case 'string':
                    $rules[] = 'string';
                    // Add max length rule if defined
                    if (isset($fieldMapping['length']) && $fieldMapping['length'] > 0) {
                        $rules[] = 'max:' . $fieldMapping['length'];
                    }
                    break;

                case 'text':
                    $rules[] = 'string';
                    break;

                case 'integer':
                case 'smallint':
                case 'bigint':
                    $rules[] = 'integer';
                    break;

                case 'boolean':
                    $rules[] = 'boolean';
                    break;

                case 'decimal':
                case 'float':
                    $rules[] = 'numeric';
                    // Add precision and scale rules if defined
                    if (isset($fieldMapping['precision']) && isset($fieldMapping['scale'])) {
                        $rules[] = 'decimal:' . $fieldMapping['scale'];
                    }
                    break;

                case 'date':
                    $rules[] = 'date';
                    break;

                case 'datetime':
                case 'datetimetz':
                    $rules[] = 'date_format:Y-m-d\TH:i:s.u\Z';
                    break;

                case 'time':
                    $rules[] = 'date_format:H:i:s';
                    break;

                case 'array':
                case 'json':
                case 'json_array':
                    $rules[] = 'array';
                    break;

                case 'simple_array':
                    $rules[] = 'string';
                    break;

                case 'guid':
                    $rules[] = 'uuid';
                    break;

                    // Add more type mappings as needed
            }
        }

        // Add nullable rule if the field is nullable
        if (isset($fieldMapping['nullable']) && $fieldMapping['nullable']) {
            $rules[] = 'nullable';
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
