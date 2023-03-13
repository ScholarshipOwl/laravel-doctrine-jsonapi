<?php

namespace Sowl\JsonApi;

use Illuminate\Contracts\Auth\Access\Gate;
use League\Fractal\Resource\Primitive;
use League\Fractal\TransformerAbstract;
use Sowl\JsonApi\Fractal\Scope;

abstract class AbstractTransformer extends TransformerAbstract
{
    protected array $availableMetas = [];

    public static function create(): static
    {
        return new static();
    }

    public function getAvailableMetas(): array
    {
        return $this->availableMetas;
    }

    public function setAvailableMetas(array $availableMetas): static
    {
        $this->availableMetas = $availableMetas;
        return $this;
    }

    public function processMetasets(Scope $scope, mixed $data): ?array
    {
        $requestedMetaset = $scope->getRequestedMetasets();

        if (is_null($requestedMetaset)) {
            return null;
        }

        $filteredMeta = array_filter($this->getAvailableMetas(), fn (string $meta) => in_array($meta, $requestedMetaset));

        $meta = [];
        foreach ($filteredMeta as $metaField) {
            $methodName = 'meta'.str_replace(
                    ' ',
                    '',
                    ucwords(str_replace(
                        '_',
                        ' ',
                        str_replace(
                            '-',
                            ' ',
                            $metaField
                        )
                    ))
                );

            if (!method_exists($this, $methodName)) {
                throw new \RuntimeException(sprintf(
                    "Method '%s::%s' must be implemented to support '%s'.",
                    static::class,
                    $methodName,
                    $metaField
                ));
            }

            $meta[$metaField] = call_user_func([$this, $methodName], $data);
        }

        return $meta;
    }

    protected function primitive($data, $transformer = null, $resourceType = null): Primitive
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
