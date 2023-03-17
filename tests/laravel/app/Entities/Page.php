<?php

namespace Tests\App\Entities;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\ResourceInterface;
use Tests\App\Transformers\PagesTransformer;

/**
 * @ORM\Entity(repositoryClass="Tests\App\Repositories\PagesRepository")
 * @ORM\Table()
 */
class Page implements ResourceInterface
{
    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id, ORM\Column]
    protected ?int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected ?string $title;

    /**
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected ?string $content;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="pages")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected ?User $user;

    /**
     * @ORM\OneToMany(targetEntity="PageComment", mappedBy="page", fetch="LAZY")
     */
    protected Collection $comments;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
    }

    public static function getResourceType(): string
    {
        return 'pages';
    }

    public static function transformer(): AbstractTransformer
    {
        return new PagesTransformer();
    }

    public static function relationships(): RelationshipsCollection
    {
        return new RelationshipsCollection([
            ToOneRelationship::create('user', User::class)
        ]);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return Collection|PageComment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }
}
