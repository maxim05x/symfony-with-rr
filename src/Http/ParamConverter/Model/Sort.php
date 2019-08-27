<?php

namespace App\Http\ParamConverter\Model;

class Sort
{
    public const DEFAULT_DIRECTION = 'ASC';

    private $fields = [];

    /**
     * @param string $field
     * @param string $direction
     * @param string $type
     */
    public function add($field, $direction, $type = Field::FIELD_OWN)
    {
        if (!in_array(strtoupper($direction), ['ASC', 'DESC'])) {
            $direction = self::DEFAULT_DIRECTION;
        }

        $this->fields[$field] = new Field($field, $direction, $type);
    }

    /**
     * @return Field[]
     */
    public function getAll(): array
    {
        return array_values($this->fields);
    }
}
