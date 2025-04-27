<?php

namespace Sowl\JsonApi\FilterParsers;

use Doctrine\Common\Collections\Criteria;
use Sowl\JsonApi\Request;

/**
 * Class extends the AbstractFilterParser and provides a specific implementation for parsing and applying search
 * filters to a Criteria object.
 *
 * The applyFilter method checks if the filter from the request is a string and if the property is set.
 * If so, it creates a LIKE expression for the given property and filter value and adds it the Criteria's.
 *
 * If the filter is an array and contains the search key, it creates a LIKE expression for the property and the value
 * of the search key, then adds it to the Criteria's 'where' clause.
 *
 * To use the SearchFilterParser class, you can instantiate it with a Request object, a property name to apply the
 * search filter on, and an optional search key. Then, you can pass a Criteria object to its applyFilter method to apply
 * the search filter from the request. The SearchFilterParser allows you to filter data using a simple search
 * mechanism based on the LIKE operator.
 */
class SearchFilterParser extends AbstractFilterParser
{
    const SEARCH_KEY = 'search';

    /**
     * The constructor accepts a Request object, a nullable property name to apply the search filter on, and an
     * optional search key (which defaults to 'search'). It calls the parent constructor to store the Request object.
     */
    public function __construct(
        Request $request,
        readonly public ?string $property,
        readonly public string $searchKey = self::SEARCH_KEY
    ) {
        parent::__construct($request);
    }

    /**
     * It processes the search filter parameters from the request and applies them to the given Criteria object using
     * the LIKE operator.
     */
    public function applyFilter(Criteria $criteria): Criteria
    {
        $filter = $this->request->getFilter();

        if (is_string($filter) && is_string($this->property)) {
            $criteria->andWhere(
                $criteria->expr()->contains($this->property, $filter)
            );
        }

        if (is_array($filter) && isset($filter[$this->searchKey])) {
            $criteria->andWhere(
                $criteria->expr()->contains($this->property, $filter[$this->searchKey])
            );
        }

        return $criteria;
    }

    public function docSpec(): ?array
    {
        return [
            'filter' => [
                'required' => false,
                'type' => 'string',
                'description' => __('jsonapi::query_params.filter.search.description', [
                    'property' => $this->property,
                ]),
            ],
        ];
    }
}
