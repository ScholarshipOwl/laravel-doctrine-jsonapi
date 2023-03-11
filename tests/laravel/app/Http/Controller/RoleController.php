<?php

namespace Tests\App\Http\Controller;

use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Default\WithListTrait;

class RoleController extends Controller
{
    use WithListTrait;

    public function searchProperty(): ?string
    {
        return 'name';
    }

    public function filterable(): array
    {
        return ['id', 'name'];
    }
}
