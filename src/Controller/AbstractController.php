<?php

namespace Sowl\JsonApi\Controller;

use Illuminate\Routing\Controller;

abstract class AbstractController extends Controller
{
    use AuthorizesRequestsTrait;

    public function __construct()
    {
        $this->authorizeResource();
    }
}
