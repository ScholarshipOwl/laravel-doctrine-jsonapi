<?php

namespace Tests\App\Actions\Page;

use Sowl\JsonApi\Action\Resource\ShowResource as BasicAction;
use Sowl\JsonApi\ResourceInterface;

class ShowPageResource extends BasicAction
{
    /**
     * Anyone can look on the pages.
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
    }
}
