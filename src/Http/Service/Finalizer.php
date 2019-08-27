<?php

namespace App\Http\Service;

use App\Http\ParamConverter\RequestCollection;
use App\Http\Service\Model\AbstractResource;
use App\Http\Service\Model\Collection;
use App\Http\Service\Model\Item;
use App\Http\Transformer\DefaultTransformer;
use App\Http\Transformer\TransformerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class Finalizer
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @param mixed $data
     * @param TransformerInterface $transformer
     * @return AbstractResource
     */
    public function resource($data, TransformerInterface $transformer = null): AbstractResource
    {
        if (is_null($transformer)) {
            $transformer = new DefaultTransformer();
        }

        if (is_iterable($data)) {
            $resource = new Collection($data, $transformer);
            $resource->setPagination($this->getPagination());
        } else {
            $resource = new Item($data, $transformer);
        }
        return $resource;
    }

    private function getPagination()
    {
        if ($currentRequest = $this->requestStack->getCurrentRequest()) {
            foreach ($currentRequest->attributes->all() as $value) {
                if ($value instanceof RequestCollection) {
                    return $value->getPagination();
                }
            }
        }
        return null;
    }
}
