<?php

namespace Tests\App\Actions\User;

use Illuminate\Contracts\Auth\Access\Gate;
use Sowl\JsonApi\Request\Resource\AbstractCreateRequest;

class CreateUserRequest extends AbstractCreateRequest
{
    use HasUsersRepositoryTrait;

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            'data.attributes.name' => 'required',
            'data.attributes.password' => 'required',
            'data.attributes.email' => 'required|email',
        ]);
    }

    public function authorize(Gate $gate): bool
    {
        return true;
    }
}
