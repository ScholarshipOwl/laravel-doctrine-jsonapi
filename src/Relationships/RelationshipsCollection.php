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
    public function add(ToOneRelationship|ToManyRelationship $relationship): static
    {
        $this->relationships[$relationship->name()] = $relationship;

        return $this;
    }

    /**
     * Retrieves a relationship by name from the collection.
     */
    public function get(string $name): ToOneRelationship|ToManyRelationship|null
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
     * @return array<string, ToOneRelationship|ToManyRelationship>
     */
    public function all(): array
    {
        return $this->relationships->all();
    }

    /**
     * Retrieves all To-One relationships.
     * @return Collection<string, ToOneRelationship>
     */
    public function toOne(): Collection
    {
        return $this->relationships->filter(fn ($rel) => $rel instanceof ToOneRelationship);
    }

    /**
     * Retrieves all To-Many relationships.
     * @return Collection<string, ToManyRelationship>
     */
    public function toMany(): Collection
    {
        return $this->relationships->filter(fn ($rel) => $rel instanceof ToManyRelationship);
    }
}
