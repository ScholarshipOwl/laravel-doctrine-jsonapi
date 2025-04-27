<?php

namespace App\Entities;

use App\Transformers\UserStatusTransformer;
use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\ORM\Facades\EntityManager;
use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\Relationships\RelationshipsCollection;
use Sowl\JsonApi\ResourceInterface;

#[ORM\Entity]
class UserStatus implements ResourceInterface
{
    const ACTIVE = '1';

    const INACTIVE = '2';

    const DELETED = '3';

    public static function active(): self
    {
        return EntityManager::getReference(self::class, self::ACTIVE);
    }

    public static function inactive(): self
    {
        return EntityManager::getReference(self::class, self::INACTIVE);
    }

    public static function deleted(): self
    {
        return EntityManager::getReference(self::class, self::DELETED);
    }

    public static function getResourceType(): string
    {
        return 'userStatuses';
    }

    public static function transformer(): AbstractTransformer
    {
        return new UserStatusTransformer;
    }

    public static function relationships(): RelationshipsCollection
    {
        return new RelationshipsCollection;
    }

    #[ORM\Id, ORM\Column(type: 'string', length: 2)]
    private ?string $id;

    #[ORM\Column(type: 'string', length: 16)]
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
