<?php

namespace Tests\App\Entities;

use Sowl\JsonApi\ResourceInterface;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Relationships\RelationshipsCollection;

use Tests\App\Transformers\UserStatusTransformer;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class UserStatus implements ResourceInterface
{
    const ACTIVE = '1';
    const INACTIVE = '2';
    const DELETED = '3';

    public static function getResourceType(): string
    {
        return 'user-statuses';
    }

    public static function transformer(): AbstractTransformer
    {
        return new UserStatusTransformer();
    }

    public static function relationships(): RelationshipsCollection
    {
        return new RelationshipsCollection();
    }

    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=2)
     */
    private ?string $id;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private ?string $name;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
