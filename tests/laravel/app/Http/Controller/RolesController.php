<?php

namespace Tests\App\Http\Controller;

use Sowl\JsonApi\Controller;

class RolesController extends Controller
{
    public function searchProperty(): ?string
    {
        return 'name';
    }

    public function filterable(): array
    {
        return ['id', 'name'];
    }
}
