<?php

namespace Sowl\JsonApi\Request;

use Doctrine\Common\Collections\Criteria;

/**
 * Provides functionality to handle pagination-related parameters of a JSON:API request.
 */
trait WithPaginationParamsTrait
{
    abstract public function get(string $key, mixed $default = null): mixed;

    /**
     * Returns an array of validation rules for pagination-related parameters.
     */
    public function paginationParamsRules(): array
    {
        return [
            'sort' => 'sometimes|required|string',
            'page' => 'sometimes|required|array',
            'page.number' => 'sometimes|required|numeric',
            'page.size' => 'sometimes|required|numeric',
            'page.limit' => 'sometimes|required|numeric',
            'page.offset' => 'sometimes|required|numeric',
        ];
    }

    /**
     * Retrieves the sort part of a JSON:API request.
     */
    public function getSort(): array
    {
        $sortBy = [];

        $sort = $this->get('sort');
        if (is_string($sort)) {
            $fields = explode(',', $sort);

            foreach ($fields as $field) {
                $direction = Criteria::ASC;
                if ($field[0] === '-') {
                    $field = substr($field, 1);
                    $direction = Criteria::DESC;
                }

                $sortBy[$field] = $direction;
            }
        }

        return $sortBy;
    }

    /**
     * Retrieves the page part of a JSON:API request.
     */
    public function getPage(): ?array
    {
        $page = $this->get('page');
        if (is_array($page)) {
            return $page;
        }

        return null;
    }

    /**
     * Retrieves the first result index for pagination from a JSON:API request.
     */
    public function getFirstResult(): ?int
    {
        $page = $this->getPage();
        $maxResults = $this->getMaxResults();

        if (is_array($page) && ! is_null($maxResults)) {
            if (isset($page['number']) && is_numeric($page['number'])) {
                return ((int) $page['number'] - 1) * $maxResults;
            }

            if (isset($page['offset']) && is_numeric($page['offset'])) {
                return (int) $page['offset'];
            }

            return 0;
        }

        return null;
    }

    /**
     * Retrieves the maximum number of results per page for pagination from a JSON:API request.
     */
    public function getMaxResults(): ?int
    {
        $page = $this->getPage();

        if (is_array($page)) {
            if (isset($page['number']) && is_numeric($page['number'])) {
                if (isset($page['size']) && is_numeric($page['size'])) {
                    return (int) $page['size'];
                }
            }

            if (isset($page['limit']) && is_numeric($page['limit'])) {
                return (int) $page['limit'];
            }

            return $this->defaultPaginationLimit();
        }

        return null;
    }

    /**
     * Returns the default pagination limit.
     */
    protected function defaultPaginationLimit(): int
    {
        return 1000;
    }
}
