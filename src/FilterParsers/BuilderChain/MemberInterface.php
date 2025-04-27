<?php

namespace Sowl\JsonApi\FilterParsers\BuilderChain;

/**
 * Interface that represents a member of the builder chain.
 *
 * It ensures that any class implementing this interface will have the __invoke method, which is the primary way to
 * interact with the chain member and modify the object being passed through the chain.
 *
 * By implementing the MemberInterface, you ensure that any class that is a part of the builder chain will have
 * a consistent way to interact with and modify objects passed through the chain. This makes it easier to compose
 * a chain of processing steps, as each step will adhere to the same interface and method signature.
 */
interface MemberInterface
{
    /**
     * Method is responsible for receiving an object that you want to build or modify within the chain.
     * The object passed to this method should be of the same type as the one being returned.
     * The implementation of this method in classes that implement this interface will typically perform some kind of
     * transformation or processing on the input object before returning it.
     *
     * @template Member
     *
     * @param  Member  $object
     * @return Member
     */
    public function __invoke($object);
}
