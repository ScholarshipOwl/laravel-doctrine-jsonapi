<?php

namespace Sowl\JsonApi\Relationships;

use Illuminate\Support\Collection;
use Sowl\JsonApi\ResourceInterface;

/**
 * @implements Collection
 */
class RelationshipsCollection
{
    protected Collection $relationships;

    public function __construct($relationships = [])
    {
        $this->relationships = new Collection();
        array_map(fn ($rel) => $this->add($rel), $relationships);
    }

    public function add(AbstractRelationship $relationship): static
    {
        $this->relationships[$relationship->name()] = $relationship;

        return $this;
    }

    public function get(string $name): ?AbstractRelationship
    {
        return $this->relationships[$name] ?? null;
    }

    public function has(string $name): bool
    {
        return $this->relationships->has($name);
    }

    /** @return array<string, AbstractRelationship> */
    public function all(): array
    {
        return $this->relationships->all();
    }

    /**
     * @return Collection<string, ToOneRelationship>
     */
    public function toOne(): Collection
    {
        return $this->relationships->filter(fn ($rel) => $rel instanceof ToOneRelationship);
    }

    /**
     * @return Collection<string, ToManyRelationship>
     */
    public function toMany(): Collection
    {
        return $this->relationships->filter(fn ($rel) => $rel instanceof ToManyRelationship);
    }
}
