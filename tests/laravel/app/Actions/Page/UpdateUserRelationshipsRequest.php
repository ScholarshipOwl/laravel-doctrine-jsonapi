<?php

namespace Tests\App\Actions\Page;

use Illuminate\Support\Facades\Gate;
use Sowl\JsonApi\Default\AbilitiesInterface;
use Sowl\JsonApi\Request;
use Sowl\JsonApi\Rules\ObjectIdentifierRule;
use Tests\App\Entities\User;

class UpdateUserRelationshipsRequest extends Request
{
    public function authorize(): bool
    {
        return Gate::allows(AbilitiesInterface::UPDATE_RELATIONSHIPS, [$this->resource(), 'user']);
    }

    public function dataRules(): array
    {
        return [
            'data' => [new ObjectIdentifierRule(User::class)]
        ];
    }
}
