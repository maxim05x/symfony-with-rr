<?php

namespace App\Http\Service\Model;

class Item extends AbstractResource
{
    public function doTransform(): array
    {
        return $this->transformer->transform($this->data);
    }
}
