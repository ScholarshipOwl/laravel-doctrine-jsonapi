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
    /**
     * The constructor accepts a Request object and an array of filterable fields. It calls the parent constructor
     * to store the Request object.
     */
    public function __construct(Request $request, protected array $filterable)
    {
        parent::__construct($request);
    }

    /**
     * It processes the filter parameters from the request and applies them to the given Criteria object.
     */
    public function applyFilter(Criteria $criteria): Criteria
    {
        $filter = $this->request->getFilter();

        if (!is_array($filter)) {
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
     * Example:
     *   filter[field][operator] = gte
     *   filter[field][value] = value
     *
     *   filter[field][operator] = not
     *   filter[field][value] = notValue
     *
     *       --> "alias.field >= value AND alias.field != notValue"
     *
     */
    protected function processOperatorFilter(Criteria $criteria, string $field, mixed $value): static
    {
        if (is_array($value) && isset($value['operator']) && array_key_exists('value', $value)) {
            $operator = $value['operator'];

            if (!method_exists($criteria->expr(), $operator)) {
                throw (new BadRequestException('Unknown filter operator.'))
                    ->error('filter-array-unknown-operator', ['field' => $field, 'filter' => $value], 'Unknown operator.');
            }

            $criteria->andWhere(
                $criteria->expr()->$operator($field, $value['value'])
            );
        }

        return $this;
    }
}
