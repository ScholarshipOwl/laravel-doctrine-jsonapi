<?php

namespace Sowl\JsonApi;

use Sowl\JsonApi\Default\Middleware\Authorize;

class Controller extends \Illuminate\Routing\Controller
{
    public function __construct()
    {
        $this->middleware(Authorize::class)
            ->except($this->noAuthMethods());
    }

    protected function noAuthMethods(): array
    {
        return [];
    }
}
