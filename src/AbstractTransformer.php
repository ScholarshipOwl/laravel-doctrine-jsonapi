<?php

namespace Sowl\JsonApi;

use Illuminate\Contracts\Auth\Access\Gate;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;
use Sowl\JsonApi\AuthenticationAbilitiesInterface;
use Sowl\JsonApi\Exceptions\ForbiddenException;

abstract class AbstractTransformer extends TransformerAbstract
{
    public static function create(): static
    {
        return new static();
    }

    protected function primitive($data, $transformer = null, $resourceKey = null): Primitive
    {
        throw new \RuntimeException('Primitive values is not supported.');
    }

    /**
     * Some includes may have some additional authorization permissions.
     * The gate can be used for verifying the permissions.
     */
    protected function gate(): Gate
    {
        return app(Gate::class);
    }
}
