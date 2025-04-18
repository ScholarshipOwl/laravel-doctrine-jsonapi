<?php

namespace Sowl\JsonApi\Scribe\Strategies;

use Knuckles\Scribe\Extracting\FindsFormRequestForMethod;
use ReflectionAttribute;

trait ReadsPhpAttributes
{
    use FindsFormRequestForMethod;

    abstract protected static function readAttributes(): array;

    protected function getAttributes(\ReflectionFunctionAbstract $method, ?\ReflectionClass $class = null): array
    {
        $attributesOnMethod = collect(static::readAttributes())
            ->flatMap(fn(string $name) => $method->getAttributes($name, ReflectionAttribute::IS_INSTANCEOF))
            ->map(fn(ReflectionAttribute $a) => $a->newInstance())->all();

        // If there's a FormRequest, we check there.
        if ($formRequestClass = $this->getFormRequestReflectionClass($method)) {
            $attributesOnFormRequest = collect(static::readAttributes())
                ->flatMap(fn(string $name) => $formRequestClass->getAttributes($name, ReflectionAttribute::IS_INSTANCEOF))
                ->map(fn(ReflectionAttribute $a) => $a->newInstance())->all();
        }

        if ($class) {
            $attributesOnController = collect(static::readAttributes())
                ->flatMap(fn(string $name) => $class->getAttributes($name, ReflectionAttribute::IS_INSTANCEOF))
                ->map(fn(ReflectionAttribute $a) => $a->newInstance())->all();
        }

        return [$attributesOnMethod, $attributesOnFormRequest ?? [], $attributesOnController ?? [], ];
    }

    protected function getAllAttributes(\ReflectionFunctionAbstract $method, ?\ReflectionClass $class = null): array
    {
        [$attributesOnMethod, $attributesOnFormRequest, $attributesOnController] =
            $this->getAttributes($method, $class);

        return [
            ...$attributesOnController,
            ...$attributesOnFormRequest,
            ...$attributesOnMethod
        ];
    }
}
