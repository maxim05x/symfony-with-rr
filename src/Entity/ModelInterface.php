<?php

namespace App\Entity;


interface ModelInterface
{
    /**
     * @return string
     */
    public function getId();

    /**
     * @return array
     */
    public static function transformIgnoredAttributes(): array;
}