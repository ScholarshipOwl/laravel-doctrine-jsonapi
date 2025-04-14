<?php

namespace Tests\App\Entities;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\ResourceInterface;
use Tests\App\Transformers\RoleTransformer;

/**
 * @ORM\Entity(repositoryClass="Tests\App\Repositories\RolesRepository")
 * @ORM\Table(name="role")
 */
class Role implements ResourceInterface
{
    const ROOT = 1;
    const ROOT_NAME = 'Root';

    const USER = 2;
    const USER_NAME = 'User';

    const MODERATOR = 3;
    const MODERATOR_NAME = 'Moderator';

    /**
     * @ORM\Id();
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected ?string $name;

    /**
     * @ORM\Column(name="permissions", type="json")
     */
    protected array $permissions = [];

    /**
     * @ORM\ManyToMany(targetEntity="User", mappedBy="roles", fetch="EXTRA_LAZY")
     */
    protected Collection $users;

    public static function getResourceType(): string
    {
        return 'roles';
    }

    public static function transformer(): AbstractTransformer
    {
        return new RoleTransformer();
    }

    public static function relationships(): RelationshipsCollection
    {
        return new RelationshipsCollection();
    }

    public static function root(): static
    {
        return EntityManager::getReference(static::class, static::ROOT);
    }

    public static function user(): static
    {
        return EntityManager::getReference(static::class, static::USER);
    }

    public static function moderator(): static
    {
        return EntityManager::getReference(static::class, static::MODERATOR);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function setPermissions(array $permissions): self
    {
        $this->permissions = $permissions;
        return $this;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }
}
