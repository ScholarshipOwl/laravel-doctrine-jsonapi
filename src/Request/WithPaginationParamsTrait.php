<?php

namespace Sowl\JsonApi\Request;

use Doctrine\Common\Collections\Criteria;

trait WithPaginationParamsTrait
{
    abstract public function get(string $key, mixed $default = null): mixed;

    public function paginationParamsRules(): array
    {
        return [
            'sort'          => 'sometimes|required|string',
            'page'          => 'sometimes|required|array',
            'page.number'   => 'sometimes|required|numeric',
            'page.size'     => 'sometimes|required|numeric',
            'page.limit'    => 'sometimes|required|numeric',
            'page.offset'   => 'sometimes|required|numeric',
        ];
    }

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

    public function getPage(): array|null
    {
        $page = $this->get('page');
        if (is_array($page)) {
            return $page;
        }

        return null;
    }

    public function getFirstResult(): int|null
    {
        $page = $this->getPage();
        $maxResults = $this->getMaxResults();

        if (is_array($page) && !is_null($maxResults)) {
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

    public function getMaxResults(): int|null
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

    protected function defaultPaginationLimit(): int
    {
        return 1000;
    }
}