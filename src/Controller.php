<?php

namespace Sowl\JsonApi;

use Sowl\JsonApi\Default\Middleware\AuthorizeAction;

class Controller extends \Illuminate\Routing\Controller
{
    public function __construct()
    {
        $this->middleware(AuthorizeAction::class)
            ->except($this->noAuthMethods());
    }

    protected function noAuthMethods(): array
    {
        return [];
    }
}