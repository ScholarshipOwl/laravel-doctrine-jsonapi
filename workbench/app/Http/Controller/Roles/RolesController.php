<?php

namespace App\Http\Controller\Roles;

use Illuminate\Routing\Controller;
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
