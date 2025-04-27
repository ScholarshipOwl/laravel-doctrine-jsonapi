<?php

namespace App\Entities;

use App\Repositories\PageCommentsRepository;
use Doctrine\ORM\Mapping as ORM;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\Relationships\ToOneRelationship;
use Sowl\JsonApi\ResourceInterface;
use App\Transformers\PageCommentTransformer;

#[ORM\Entity(repositoryClass: PageCommentsRepository::class)]
#[ORM\Table]
class PageComment implements ResourceInterface
{
    const FIRST = '00000000-0000-0000-0000-000000000001';

    const SECOND = '00000000-0000-0000-0000-000000000002';

    const THIRD = '00000000-0000-0000-0000-000000000003';

    #[ORM\Id, ORM\Column(type: 'guid')]
    protected ?string $id;

    #[ORM\Column(name: 'content', type: 'string', length: 1023, nullable: false)]
    protected ?string $content;

    #[ORM\ManyToOne(targetEntity: Page::class, inversedBy: 'comments'), ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected ?Page $page;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'pageComments'), ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    protected ?User $user;

    public static function getResourceType(): string
    {
        return 'pageComments';
    }

    public static function transformer(): AbstractTransformer
    {
        return new PageCommentTransformer;
    }

    public static function relationships(): RelationshipsCollection
    {
        return new RelationshipsCollection([
            ToOneRelationship::create('user', User::class),
            ToOneRelationship::create('page', Page::class),
        ]);
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setPage(Page $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function getPage(): Page
    {
        return $this->page;
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

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
