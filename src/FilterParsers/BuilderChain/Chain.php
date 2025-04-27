<?php

namespace Sowl\JsonApi\FilterParsers\BuilderChain;

/**
 * Class is a simple implementation of a chain of responsibility pattern.
 * It is designed to process a given object by applying a series of members (callable functions or
 * MemberInterface instances) in a sequential order.
 *
 * By using this Chain class, you can create a flexible and extensible processing pipeline that allows you to apply
 * a series of transformations or filters to a given object in a sequential and organized manner.
 */
class Chain
{
    protected array $members = [];

    /**
     * Static method creates a new instance of the Chain class with the given array of members as its initial state.
     */
    public static function create(array $members = []): static
    {
        return new static($members);
    }

    /**
     * The constructor accepts an array of members and adds them to the chain using the add() method.
     */
    final public function __construct(array $members = [])
    {
        $this->add($members);
    }

    /**
     * This method adds one or more members to the chain. If an array is given, it iterates through the array and
     * adds each item individually. If a single MemberInterface instance or a callable function is provided,
     * it is added directly to the members array.
     *
     * The method returns the current Chain instance to allow method chaining.
     */
    public function add(array|MemberInterface|callable $member): static
    {
        if (is_array($member)) {
            foreach ($member as $item) {
                $this->add($item);
            }

            return $this;
        }

        $this->members[] = $member;

        return $this;
    }

    /**
     * Method accepts a mixed object as its argument and processes it by applying each member in the chain sequentially.
     * It calls each member as a function with the object as its argument and updates the object with the result.
     * Once all members have been applied, the method returns the final processed object.
     */
    public function process(mixed $object): mixed
    {
        foreach ($this->members as $member) {
            $object = call_user_func($member, $object);
        }

        return $object;
    }
}
