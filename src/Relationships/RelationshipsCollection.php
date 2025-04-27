<?php

namespace Sowl\JsonApi\Relationships;

use Illuminate\Support\Collection;

/**
 * Class represents a collection of resource relationships.
 * The class provides methods to add, get, and check for the existence of relationships in the collection.
 * It also provides methods to retrieve all relationships, To-One or To-Many relationships.
 */
class RelationshipsCollection
{
    protected Collection $relationships;

    public function __construct($relationships = [])
    {
        $this->relationships = new Collection();
        array_map(fn ($rel) => $this->add($rel), $relationships);
    }

    /**
     * Adds a relationship to the collection.
     */
    public function add(RelationshipInterface $relationship): static
    {
        $this->relationships[$relationship->name()] = $relationship;

        return $this;
    }

    /**
     * Adds a To-One relationship to the collection.
     */
    public function addToOne(string $name, string $class, ?string $property = null): self
    {
        $this->add(new ToOneRelationship($name, $class, $property));

        return $this;
    }

    /**
     * Adds a To-Many relationship to the collection.
     */
    public function addToMany(string $name, string $class, string $mappedBy, ?string $property = null): self
    {
        $this->add(new ToManyRelationship($name, $class, $mappedBy, $property));

        return $this;
    }

    /**
     * Retrieves a relationship by name from the collection.
     */
    public function get(string $name): RelationshipInterface|null
    {
        return $this->relationships[$name] ?? null;
    }

    /**
     * Checks if the collection has a relationship with the given name.
     */
    public function has(string $name): bool
    {
        return $this->relationships->has($name);
    }

    /**
     * Returns all relationships in the collection.
     *
     * @return array<string, RelationshipInterface>
     */
    public function all(): array
    {
        return $this->relationships->all();
    }


    /**
     * Maps over the relationships in the collection.
     * @param callable(RelationshipInterface): RelationshipInterface $callback
     */
    public function map(callable $callback): Collection
    {
        return $this->relationships->map($callback);
    }

    /**
     * Retrieves all To-One relationships.
     * @return static<string, ToOneRelationship>
     */
    public function toOne(): static
    {
        return new static($this->relationships->filter(fn ($rel) => $rel instanceof ToOneRelationship)->all());
    }

    /**
     * Retrieves all To-Many relationships.
     * @return static<string, ToManyRelationship>
     */
    public function toMany(): static
    {
        return new static($this->relationships->filter(fn ($rel) => $rel instanceof ToManyRelationship)->all());
    }
}
