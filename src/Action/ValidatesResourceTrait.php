<?php

namespace Sowl\JsonApi\Action;

use Sowl\JsonApi\Exceptions\ConstraintViolationException;
use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\ResourceRepository;
use Symfony\Component\Validator\Validation;

/**
 * If we want to enable and use this trait we need to install  "symfony/validator"
 * @deprecated
 */
trait ValidatesResourceTrait
{
    abstract public function repository(): ResourceRepository;

    protected function validateResource(ResourceInterface $resource): ResourceInterface
    {
        $errors = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator()
            ->validate($resource);

        if ($errors->count() > 0) {
            throw new ConstraintViolationException($errors);
        }

        return $resource;
    }
}
