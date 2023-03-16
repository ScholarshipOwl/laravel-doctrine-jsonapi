<?php

namespace Sowl\JsonApi;

use Sowl\JsonApi\Default\Middleware\Authorize;

/**
 * This class extends the base Illuminate\Routing\Controller class and provides a middleware to handle authorization
 * for JSON:API requests. Any JSON:API controller should inherit this class so that authorization will be applied
 * for all the endpoints.
 */
class Controller extends \Illuminate\Routing\Controller
{
    /**
     * Constructor method that sets up the authorization middleware for the controller.
     * Middleware is applied to all methods except those specified in noAuthMethods().
     */
    public function __construct()
    {
        $this->middleware(Authorize::class)
            ->except($this->noAuthMethods());
    }

    /**
     * Returns an array of method names that do not require authorization middleware.
     */
    protected function noAuthMethods(): array
    {
        return [];
    }
}
