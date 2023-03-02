<?php

namespace Tests\App\Actions\Page;

use Sowl\JsonApi\Action\Resource\ShowResourceAction;
use Sowl\JsonApi\ResourceInterface;

class ShowRelatedCommentsAction extends ShowResourceAction
{
    /**
     * Anyone can look on the page's comments.
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
    }
}
