<?php

declare(strict_types=1);

namespace Tests\App\Entities;

use Doctrine\ORM\Mapping as ORM;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\ResourceInterface;
use Tests\App\Transformers\UserConfigTransformer;

#[ORM\Entity]
#[ORM\Table(name: 'user_configs')]
class UserConfig implements ResourceInterface
{
    /**
     * @ORM\Id()
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="config")
     *
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    #[ORM\Id, ORM\OneToOne(targetEntity: User::class, inversedBy: 'config'), ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    protected ?User $user = null;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $theme = null;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     */
    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $notificationsEnabled = true;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $language = null;

    public function getUser(): User
    {
        return $this->user;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): void
    {
        $this->theme = $theme;
    }

    public function isNotificationsEnabled(): bool
    {
        return $this->notificationsEnabled;
    }

    public function setNotificationsEnabled(bool $notificationsEnabled): void
    {
        $this->notificationsEnabled = $notificationsEnabled;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): void
    {
        $this->language = $language;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public static function getResourceType(): string
    {
        return 'userConfigs';
    }

    public static function relationships(): RelationshipsCollection
    {
        return new RelationshipsCollection([
            new ToOneRelationship('user', User::class),
        ]);
    }

    public static function transformer(): AbstractTransformer
    {
        return new UserConfigTransformer;
    }

    public function getId(): null|string|int
    {
        return $this->user->getId();
    }
}
