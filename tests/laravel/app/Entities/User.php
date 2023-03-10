<?php namespace Tests\App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\Access\Authorizable;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use LaravelDoctrine\ORM\Auth\Authenticatable;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Relationships\MemoizeRelationshipsTrait;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\Relationships\ToManyRelationship;
use Sowl\JsonApi\ResourceInterface;
use Tests\App\Transformers\UserTransformer;

/**
 * @ORM\Entity(repositoryClass="Tests\App\Repositories\UsersRepository")
 * @ORM\Table(name="users")
 */
class User implements AuthenticatableContract,
                      AuthorizableContract,
                      CanResetPasswordContract,
                      ResourceInterface
{
    use CanResetPassword;
    use Authenticatable;
    use Authorizable;
    use MemoizeRelationshipsTrait;
    use Timestamps;

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

    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected ?int $id;

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

    public function getId(): ?int
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

    public function addRoles(Role $role): static
    {
        if (!$this->roles->contains($role)) {
            $this->roles->add($role);
        }

        return $this;
    }

    public function removeRoles(Role $role): static
    {
        if ($role !== Role::user()){
            $this->roles->removeElement($role);
        }

        return $this;
    }

    public function isRoot(): bool
    {
        return $this->getRoles()
            ->contains(EntityManager::getReference(Role::class, Role::ROOT));
    }
}
