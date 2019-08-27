<?php

namespace App\Http\Service\Model;

use App\Http\Transformer\TransformerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractResource
{
    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var TransformerInterface
     */
    protected $transformer;

    public function __construct($data, TransformerInterface $transformer)
    {
        $this->data = $data;
        $this->transformer = $transformer;
    }

    public function asResponse($code = Response::HTTP_OK, array $metadata = []): JsonResponse
    {
        $data = $this->doTransform();
        if (!empty($metadata)) {
            $data['meta'] = array_merge($data['meta'] ?? [], $metadata);
        }

        return new JsonResponse($data, $code);
    }

    abstract public function doTransform(): array;

}
