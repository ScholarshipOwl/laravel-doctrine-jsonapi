<?php

namespace Tests\App\Actions\PageComment;

use Sowl\JsonApi\Action\Relationships\ToOne\ShowRelated;
use Sowl\JsonApi\ResourceInterface;

class ShowRelatedPage extends ShowRelated
{
    /**
     * Anyone can the page of the comment.
     */
    public function authorize(?ResourceInterface $resource = null): void
    {
    }
}
