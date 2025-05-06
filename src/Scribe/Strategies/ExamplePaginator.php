<?php

namespace Sowl\JsonApi\Scribe\Strategies;

use League\Fractal\Pagination\PaginatorInterface;

class ExamplePaginator implements PaginatorInterface
{
    protected string $urlPattern;

    public function __construct(
        protected string $path,
        protected int $currentPage = 1,
        protected int $lastPage = 5,
        protected int $total = 100,
        protected int $count = 20,
        protected int $perPage = 20,
    ) {
        $this->urlPattern = $path . '?' . http_build_query([
            'page' => [
                'number' => $currentPage,
                'size' => $perPage,
            ],
        ]);
    }

    /**
     * Get the current page.
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Get the last page.
     */
    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * Get the total number of items.
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * Get the number of items for the current page.
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * Get the number of items per page.
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * Get the url for the given page.
     */
    public function getUrl(int $page): string
    {
        // Replace placeholders in the pattern
        return str_replace(['{page}', '{perPage}'], [$page, $this->perPage], $this->urlPattern);
    }
}
