<?php

namespace App\Http\Controller\Roles;

use Sowl\JsonApi\Controller;
use Sowl\JsonApi\Default\WithListTrait;

class RolesController extends Controller
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
