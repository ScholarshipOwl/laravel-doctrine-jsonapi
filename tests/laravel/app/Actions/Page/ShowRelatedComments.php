<?php

namespace Tests\App\Actions\Page;

use Sowl\JsonApi\Action\Resource\ShowResource;
use Sowl\JsonApi\ResourceInterface;

class ShowRelatedComments extends ShowResource
{
    /**
     * Anyone can look on the page's comments.
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
    }
}
