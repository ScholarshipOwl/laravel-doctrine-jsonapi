<?php

namespace Tests\App\Http\Controller;

use Sowl\JsonApi\AuthenticationAbilitiesInterface;
use Sowl\JsonApi\Controller;

class PageCommentController extends Controller
{
    protected function methodToAbilityMap(): array
    {
        return [
            'show' => null,
        ] + parent::methodToAbilityMap();
    }
}
