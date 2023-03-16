<?php

namespace Sowl\JsonApi\Fractal;

use League\Fractal\Manager;
use Sowl\JsonApi\AbstractTransformer;

class Scope extends \League\Fractal\Scope
{
    protected Manager $manager;

    public function getRequestedMetasets(): ?array
    {
        return $this->getManager()->getMetaset($this->getResourceType());
    }

    /**
     * @param AbstractTransformer $transformer
     * @param mixed $data
     * @return array
     */
    protected function fireTransformer($transformer, $data): array
    {
        list($transformedData, $includedData) = parent::fireTransformer($transformer, $data);

        if (!empty($transformer->getAvailableMetas())) {
            if (null !== ($meta = $transformer->processMetasets($this, $data))) {
                $transformedData['meta'] = (object) array_merge(
                    $transformedData['meta'] ?? [], $meta
                );
            }
        }

        return [$transformedData, $includedData];
    }

    public function getManager(): Fractal
    {
        return $this->manager;
    }
}
