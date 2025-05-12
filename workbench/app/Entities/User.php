<?php

namespace App\Entities;

use App\Repositories\UsersRepository;
use App\Transformers\UserTransformer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use LaravelDoctrine\ORM\Auth\Authenticatable;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Concerns\HasTimestamps;
use Sowl\JsonApi\Default\Resource\WithFilterParsersTrait;
use Sowl\JsonApi\Relationships\WithRelationships;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\Resource\FilterableInterface;
use Sowl\JsonApi\ResourceInterface;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
#[ORM\Table(name: 'users')]
class User implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, FilterableInterface, ResourceInterface
{
    use Authenticatable;
    use Authorizable;
    use CanResetPassword;
    use HasTimestamps;
    use WithRelationships;
    use WithFilterParsersTrait;

    public const USER_ID = '8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b';

    public const ROOT_ID = 'f1d2f365-e9aa-4844-8eb7-36e0df7a396d';

    public const MODERATOR_ID = 'ccf660b9-3cf7-4f58-a5f7-22e53ad836f8';

    public static function getResourceType(): string
    {
        return 'users';
    }

    public static function transformer(): AbstractTransformer
    {
        return new UserTransformer();
    }

    public static function relationships(): RelationshipsCollection
    {
        return static::resolveRelationships(fn () => [
            ToOneRelationship::create('status', UserStatus::class, 'status'),
            ToManyRelationship::create('roles', Role::class, 'users'),
        ]);
    }

    public static function searchProperty(): ?string
    {
        return 'email';
    }

    public static function filterable(): array
    {
        return ['id', 'email', 'name'];
    }

    #[ORM\Id]
    #[ORM\Column(type: 'guid')]
    protected ?string $id;

    #[ORM\Column(name: 'email', type: 'string', unique: true, nullable: false)]
    protected ?string $email;

    #[ORM\Column(name: 'name', type: 'string', unique: false, nullable: false)]
    protected ?string $name;

    #[ORM\ManyToOne(targetEntity: 'UserStatus')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?UserStatus $status;

    #[ORM\ManyToMany(targetEntity: 'Role')]
    #[ORM\JoinTable(
        joinColumns: [
            new ORM\JoinColumn(
                name: 'user_id',
                referencedColumnName: 'id',
                nullable: false
            ),
        ],
        inverseJoinColumns: [
            new ORM\JoinColumn(
                name: 'role_id',
                referencedColumnName: 'id',
                nullable: false
            ),
        ]
    )]
    protected Collection $roles;

    #[ORM\OneToMany(targetEntity: 'Page', mappedBy: 'user', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected Collection $pages;

    #[ORM\OneToMany(targetEntity: 'PageComment', mappedBy: 'user', fetch: 'EXTRA_LAZY')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    protected Collection $pageComments;

    #[ORM\OneToOne(targetEntity: 'UserConfig', mappedBy: 'user', cascade: ['persist', 'remove'])]
    protected ?UserConfig $config = null;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    public function __toString(): string
    {
        return sprintf('Account %d name: %s', $this->getId(), $this->getName());
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setStatus(UserStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): static
    {
        if (! $this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(Role $role): static
    {
        if ($role !== Role::user()) {
            $this->roles->removeElement($role);
        }

        return $this;
    }

    public function isRoot(): bool
    {
        return $this->hasRoleByName(Role::ROOT_NAME);
    }

    public function hasRole(Role $role): bool
    {
        return $this->roles->contains($role);
    }

    public function hasRoleByName(string $name): bool
    {
        return $this->roles->exists(fn (int $_, Role $role) => $role->getName() === $name);
    }

    public function getConfig(): ?UserConfig
    {
        return $this->config;
    }

    public function setConfig(UserConfig $config): void
    {
        $this->config = $config;
    }
}
