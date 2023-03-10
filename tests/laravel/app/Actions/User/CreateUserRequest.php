<?php

namespace Tests\App\Actions\User;

use Sowl\JsonApi\Request;

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
}
