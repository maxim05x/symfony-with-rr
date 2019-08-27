<?php

namespace App\Http\Transformer;

use App\Entity\ModelInterface;

interface TransformerInterface
{
    public function transform(ModelInterface $model): array;
}