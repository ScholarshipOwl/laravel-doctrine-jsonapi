<?php

namespace Tests\App\Http\Controller;


use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Default\WithShowTrait;

class PageCommentController extends Controller
{
    use WithShowTrait;

    protected function methodToAbilityMap(): array
    {
        return [
            'show' => null,
        ] + parent::methodToAbilityMap();
    }
}
