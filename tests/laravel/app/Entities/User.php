<?php namespace Tests\App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use LaravelDoctrine\ACL\Roles\HasRoles;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use LaravelDoctrine\ORM\Auth\Authenticatable;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Default\Resource\WithFilterParsersTrait;
use Sowl\JsonApi\Relationships\MemoizeRelationshipsTrait;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\Resource\FilterableInterface;
use Sowl\JsonApi\ResourceInterface;
use Tests\App\Transformers\UserTransformer;

/**
 * @ORM\Entity(repositoryClass="Tests\App\Repositories\UsersRepository")
 * @ORM\Table(name="users")
 */
class User implements AuthenticatableContract,
                      AuthorizableContract,
                      CanResetPasswordContract,
                      ResourceInterface,
                      FilterableInterface
{
    use CanResetPassword;
    use Authenticatable;
    use Authorizable;
    use HasRoles;
    use MemoizeRelationshipsTrait;
    use Timestamps;
    use WithFilterParsersTrait;

    const USER_ID = '8a41dde6-b1f5-4c40-a12d-d96c6d9ef90b';
    const ROOT_ID = 'f1d2f365-e9aa-4844-8eb7-36e0df7a396d';
    const MODERATOR_ID = 'ccf660b9-3cf7-4f58-a5f7-22e53ad836f8';

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
        return static::memoizeRelationships(fn () => [
            ToManyRelationship::create('roles', Role::class, 'users')
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

    /**
     * @ORM\Id()
     * @ORM\Column(type="guid")
     */
    protected ?string $id;

    /**
     * @ORM\Column(name="email", type="string", unique=true, nullable=false)
     */
    protected ?string $email;

    /**
     * @ORM\Column(name="name", type="string", unique=false, nullable=false)
     */
    protected ?string $name;

    /**
     * @ORM\Column(name="password", type="string", nullable=false)
     */
    protected $password;

    /**
     * @ORM\ManyToMany(targetEntity="Role")
     * @ORM\JoinTable(
     *     joinColumns={
     *         @ORM\JoinColumn(
     *             name="user_id",
     *             referencedColumnName="id",
     *             nullable=false
     *         )
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(
     *             name="role_id",
     *             referencedColumnName="id",
     *             nullable=false
     *         )
     *     }
     * )
     */
    protected Collection $roles;

    /**
     * @ORM\OneToMany(targetEntity="Page", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected Collection $pages;

    /**
     * @ORM\OneToMany(targetEntity="PageComment", mappedBy="user", fetch="EXTRA_LAZY")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected Collection $pageComments;

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

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function addRole(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRole(Role $role): static
    {
        if ($role !== Role::user()){
            $this->roles->removeElement($role);
        }

        return $this;
    }

    public function isRoot(): bool
    {
        return $this->hasRoleByName(Role::ROOT_NAME);
    }
}
