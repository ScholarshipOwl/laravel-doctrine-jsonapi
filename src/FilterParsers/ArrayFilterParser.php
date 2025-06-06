<?php

namespace Sowl\JsonApi\FilterParsers;

use Doctrine\Common\Collections\Criteria;
use Sowl\JsonApi\Exceptions\BadRequestException;
use Sowl\JsonApi\Request;

/**
 * Class extends the AbstractFilterParser and provides a specific implementation for parsing and applying array-based
 * filters to a Criteria object.
 *
 * To use the ArrayFilterParser class, you can instantiate it with a Request object and an array of filterable fields.
 * Then, you can pass a Criteria object to its applyFilter method to apply the filters from the request.
 * The ArrayFilterParser allows you to filter data using different filtering strategies, like
 * equal, between, and operator-based filters.
 */
class ArrayFilterParser extends AbstractFilterParser
{
    public const OPERATORS = [
        'eq',
        'gt',
        'gte',
        'lt',
        'lte',
        'neq',
        'in',
        'notIn',
        'contains',
        'startsWith',
        'endsWith',
        'isNull',
        'isNotNull',
    ];

    public const OPERATORS_WITHOUT_VALUE = [
        'isNull',
        'isNotNull',
    ];

    /**
     * The constructor accepts a Request object and an array of filterable fields. It calls the parent constructor
     * to store the Request object.
     */
    public function __construct(
        Request $request,
        readonly public array $filterable
    ) {
        parent::__construct($request);
    }

    /**
     * It processes the filter parameters from the request and applies them to the given Criteria object.
     */
    public function applyFilter(Criteria $criteria): Criteria
    {
        $filter = $this->request->getFilter();

        if (! is_array($filter)) {
            return $criteria;
        }

        foreach ($this->filterable as $field) {
            if (array_key_exists($field, $filter)) {
                $this->processEqualFilter($criteria, $field, $filter[$field]);
                $this->processBetweenFilter($criteria, $field, $filter[$field]);
                $this->processOperatorFilter($criteria, $field, $filter[$field]);
            }
        }

        return $criteria;
    }

    /**
     * Method processes "equal" filters and updates the given Criteria object.
     *
     * If provided value is string then "equal" assertion will be applied.
     *
     * Example:
     *   filter[field]=value
     *
     *     ---> "alias.field = value" condition will be added.
     */
    protected function processEqualFilter(Criteria $criteria, string $field, mixed $value): static
    {
        if (is_string($value)) {
            $criteria->andWhere(
                $criteria->expr()->eq($field, $value)
            );
        }

        return $this;
    }

    /**
     * Method processes "between" filters and updates the given Criteria object.
     *
     * If the value is an array with 'start' and 'end' keys, it creates a range expression for the
     * given field and value, and adds it to the Criteria's 'where' clause.
     *
     * Example:
     *   filter[field][start]=fromValue
     *   filter[field][end]=toValue
     *
     *     ---> "alias.field >= fromValue AND alias.field <= toValue" condition will be added.
     */
    protected function processBetweenFilter(Criteria $criteria, string $field, mixed $value): static
    {
        if (is_array($value) && isset($value['start']) && isset($value['end'])) {
            $criteria->andWhere($criteria->expr()->andX(
                $criteria->expr()->gte($field, $value['start']),
                $criteria->expr()->lte($field, $value['end'])
            ));
        }

        return $this;
    }

    /**
     * Method processes "operator" filters and updates the given Criteria object.
     *
     * If the value is an array with 'operator' and 'value' keys, it validates the operator and creates an expression
     * with the given operator, field, and value. The expression is then added to the Criteria's 'where' clause.
     * Operator filter is usable as multiple conditions can be applied to same field.
     *
     * Operators:
     *   - eq
     *   - gt
     *   - gte
     *   - lt
     *   - lte
     *   - neq
     *   - in
     *   - notIn
     *   - contains
     *   - startsWith
     *   - endsWith
     *
     * Example:
     *   filter[field][operator] = gte
     *   filter[field][value] = value
     *
     *   filter[field][operator] = not
     *   filter[field][value] = notValue
     *
     *       --> "alias.field >= value AND alias.field != notValue"
     */
    protected function processOperatorFilter(Criteria $criteria, string $field, mixed $value): static
    {
        if (is_array($value) && is_string($value['operator'] ?? false)) {
            $operator = $value['operator'];

            if (! in_array($operator, static::OPERATORS)) {
                throw (new BadRequestException('Unknown array filter operator.'))
                    ->detail('Unknown operator.', sprintf('filter/%s/operator/%s', $field, $operator), [
                        'source' => [
                            'operator' => $operator,
                            'field' => $field,
                        ],
                    ]);
            }

            if (in_array($operator, static::OPERATORS_WITHOUT_VALUE)) {
                $criteria->andWhere(
                    $criteria->expr()->$operator($field)
                );
            } else {
                $criteria->andWhere(
                    $criteria->expr()->$operator($field, $value['value'] ?? null)
                );
            }
        }

        return $this;
    }

    public function docSpec(): ?array
    {
        $spec = [];
        foreach ($this->filterable as $field) {
            $operators = array_map(fn ($op) => "`$op`", self::OPERATORS);
            $spec["filter[$field]"] = [
                'required' => false,
                'type' => 'string',
                'description' => __(
                    'jsonapi::query_params.filter.array.description',
                    [
                        'field' => $field,
                        'operators' => implode(', ', $operators),
                    ]
                ),
            ];
        }

        return $spec;
    }
}
