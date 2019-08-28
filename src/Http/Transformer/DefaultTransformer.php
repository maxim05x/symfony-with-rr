<?php

namespace App\Http\Transformer;

use App\Entity\ModelInterface;
use GBProd\UuidNormalizer\UuidDenormalizer;
use GBProd\UuidNormalizer\UuidNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DefaultTransformer implements TransformerInterface
{
    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct()
    {
        $this->serializer = new Serializer([
            new UuidNormalizer(),
            new UuidDenormalizer(),
            new ObjectNormalizer(),
        ]);
    }

    public function transform(ModelInterface $model): array
    {
        return (array)$this->serializer->normalize($model, null, ['ignored_attributes' => $model::transformIgnoredAttributes()]);
    }

}
