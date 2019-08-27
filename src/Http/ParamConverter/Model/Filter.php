<?php

namespace App\Http\ParamConverter\Model;

class Filter
{
    private $fields = [];

    /**
     * @param string $field
     * @param mixed $value
     * @param string $type
     * @param string $filter
     */
    public function add($field, $value, $type = Field::FIELD_OWN, $filter = Field::FILTER_SINGLE)
    {
        $this->fields[$field] = new Field($field, $value, $type, $filter);
    }

    /**
     * @return Field[]
     */
    public function getAll(): array
    {
        return array_values($this->fields);
    }
}
