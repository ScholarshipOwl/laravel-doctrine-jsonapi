<?php

namespace Tests\App\Http\Controller\Users;

use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ResourceIdentifierRule;
use Tests\App\Entities\UserStatus;

class CreateUserRequest extends Request
{
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'data.attributes.name' => 'required',
            'data.attributes.password' => 'required',
            'data.attributes.email' => 'required|email',
        ]);
    }

    public function attributes(): array
    {
        return [
            'data.attributes.name' => 'name',
            'data.attributes.password' => 'password',
            'data.attributes.email' => 'email',
        ];
    }
}
