<?php

namespace Sowl\JsonApi\Relationships;

use Sowl\JsonApi\AbstractTransformer;
use Sowl\JsonApi\ResourceManager;
use Sowl\JsonApi\ResourceRepository;
use Sowl\JsonApi\Rules\ResourceIdentifierRule;

interface RelationshipInterface
{
    public function isToOne(): bool;
    public function isToMany(): bool;
    public function name(): string;
    public function class(): string;
    public function property(): string;
    public function rm(): ResourceManager;
    public function repository(): ResourceRepository;
    public function transformer(): AbstractTransformer;
    public function objectIdentifierRule(): ResourceIdentifierRule;
}
