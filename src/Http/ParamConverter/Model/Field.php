<?php

namespace App\Http\ParamConverter\Model;

class Field
{
    public const FIELD_OWN = 'own';
    public const FIELD_EMBEDDED = 'embedded';
    public const FIELD_RELATED = 'related';

    public const FILTER_SINGLE = 'single';
    public const FILTER_MULTIPLE = 'multiple';
    public const FILTER_CONTAINS = 'contains';

    /**
     * @var string
     */
    private $field;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $filter;

    /**
     * @param string $field
     * @param mixed $value
     * @param string $type
     * @param string $filter
     */
    public function __construct(string $field, $value, $type = self::FIELD_OWN, $filter = self::FILTER_SINGLE)
    {
        $this->field = $field;
        $this->value = $value;
        $this->type = $type;
        $this->filter = $filter;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getFilter(): string
    {
        return $this->filter;
    }
}
