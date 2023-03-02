<?php

namespace Tests\App\Http\Controller;


use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Default\WithShowTrait;

class PageCommentController extends Controller
{
    use WithShowTrait;

    protected function noAuthMethods(): array
    {
        return [
            'show'
        ];
    }
}
