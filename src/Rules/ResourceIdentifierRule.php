<?php

namespace Sowl\JsonApi\Rules;

use Doctrine\ORM\EntityManager;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Sowl\JsonApi\ResourceManager;

/**
 * Validates if the `data` key contains an `id` of the entity with required `type`.
 * The target entity must implement JsonApiResource
 */
class ResourceIdentifierRule implements Rule
{
    protected array $message = ['The :attribute field is invalid.'];

    static public function make(
        string $resourceClass,
        mixed $rule = null,
        array $messages = []
    ): static
    {
        return new static($resourceClass, $rule, $messages);
    }

    public function __construct(
        protected ?string $resourceClass = null,
        protected mixed $rule = null,
        protected ?array $messages = [],
    )
    {
        if (!is_null($this->resourceClass)) {
            $this->rm()->verifyResourceInterface($this->resourceClass);
        }
    }

    public function withRule(mixed $rule): static
    {
        $this->rule = $rule;
        return $this;
    }

    public function withMessages(array $messages): static
    {
        $this->messages = $messages;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function passes($attribute, $value): bool
    {
        $valid = false;

        try {
            if (is_array($value)) {
                $resource = $this->rm()->objectIdentifierToResource($value, $this->resourceClass);
                $valid = true;
            }
        } catch (\InvalidArgumentException $e) {
            $this->message[] = $e->getMessage();
            return false;
        }

        if ($valid && !is_null($this->rule)) {
            $validator = Validator::make(['data' => $resource], ['data' => $this->rule], $this->messages);

            if ($validator->fails()) {
                $this->message = $validator->getMessageBag()->get('data');
                return false;
            }

            $valid = true;
        }

        return $valid;
    }

    /**
     * @inheritdoc
     */
    public function message(): array|string
    {
        return $this->message;
    }

    protected function rm(): ResourceManager
    {
        return app(ResourceManager::class);
    }

    protected function em(): EntityManager
    {
        return app(EntityManager::class);
    }
}
