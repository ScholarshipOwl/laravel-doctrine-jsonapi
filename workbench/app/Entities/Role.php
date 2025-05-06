<?php

namespace App\Entities;

use App\Repositories\RolesRepository;
use App\Transformers\RoleTransformer;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\ResourceInterface;

#[ORM\Entity(repositoryClass: RolesRepository::class)]
#[ORM\Table(name: 'role')]
class Role implements ResourceInterface
{
    public const ROOT = '1';

    public const ROOT_NAME = 'Root';

    public const USER = '2';

    public const USER_NAME = 'User';

    public const MODERATOR = '3';

    public const MODERATOR_NAME = 'Moderator';

    #[ORM\Id, ORM\Column(name: 'id', type: 'integer'), ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id;

    #[ORM\Column(name: 'name', type: 'string', length: 255)]
    protected ?string $name;

    #[ORM\Column(name: 'permissions', type: 'json')]
    protected array $permissions = [];

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'roles', fetch: 'EXTRA_LAZY')]
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
