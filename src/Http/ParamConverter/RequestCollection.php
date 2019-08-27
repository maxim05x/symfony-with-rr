<?php

namespace App\Http\ParamConverter;

use App\Http\ParamConverter\Exception\RequestCollectionFieldException;
use App\Http\ParamConverter\Model\Field;
use App\Http\ParamConverter\Model\Filter;
use App\Http\ParamConverter\Model\Pagination;
use App\Http\ParamConverter\Model\Search;
use App\Http\ParamConverter\Model\Sort;

class RequestCollection
{
    public const FILTERED = 'filtered';
    public const SORTABLE = 'sortable';

    /**
     * @var Search
     */
    protected $search;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Sort
     */
    protected $sort;

    /**
     * @var Pagination
     */
    protected $pagination;

    /**
     * simple array: ['field1', 'field2', ...]
     * or
     * complex array:
     * [
     *   'field1' => ['filtered' => true, 'sortable' => true, 'mapping' => 'name_of_field'],
     *   'field2' => ['sortable' => false, 'type' => FIELD_TYPE, 'filter' => FILTER_SINGLE],
     *   ...
     * ]
     * @var array
     */
    protected $allowedFields = [];

    /**
     * @var bool
     */
    protected $paginationDisabled = false;

    public function __construct()
    {
        $this->search = new Search();
        $this->filter = new Filter();
        $this->sort = new Sort();
        $this->pagination = new Pagination();

        if ($this->paginationDisabled) {
            $this->pagination->disable();
        }

        $this->normalizeFields();
    }

    /**
     * @return Search
     */
    public function getSearch(): Search
    {
        return $this->search;
    }

    /**
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * @return Sort
     */
    public function getSort(): Sort
    {
        return $this->sort;
    }

    /**
     * @return Pagination
     */
    public function getPagination(): Pagination
    {
        return $this->pagination;
    }

    /**
     * @param string $field
     * @param null|mixed $value
     * @throws RequestCollectionFieldException
     */
    public function addFilter(string $field, $value = null)
    {
        if ($this->isAllowField($field, self::FILTERED) && !is_null($value)) {
            $this->filter->add(
                $this->getFieldMapping($field),
                $value,
                $this->getFieldType($field),
                $this->getFieldFilter($field)
            );
        }
    }

    /**
     * @param string $field
     * @param string $direction
     * @throws RequestCollectionFieldException
     */
    public function addSort(string $field, $direction = Sort::DEFAULT_DIRECTION)
    {
        if ($this->isAllowField($field, self::SORTABLE)) {
            $this->sort->add(
                $this->getFieldMapping($field),
                $direction,
                $this->getFieldType($field)
            );
        }
    }

    /**
     * @param string $query
     */
    public function updateSearch(string $query)
    {
        $this->search->update($query);
    }

    /**
     * @param int $offset
     * @param int $limit
     * @param int $before
     */
    public function updatePagination(int $offset, int $limit, int $before = 0)
    {
        $this->pagination->update($offset, $limit, $before);
    }

    /**
     * @param string $field
     * @param string $type
     * @return bool
     */
    protected function isAllowField(string $field, $type): bool
    {
        if (empty($this->allowedFields)) {
            return true;
        }
        if (array_key_exists($field, $this->allowedFields) && $this->allowedFields[$field][$type]) {
            return true;
        }
        return false;
    }

    /**
     * @param string $field
     * @return mixed
     * @throws RequestCollectionFieldException
     */
    protected function getFieldType(string $field)
    {
        if (array_key_exists($field, $this->allowedFields)) {
            return $this->allowedFields[$field]['type'];
        }
        throw new RequestCollectionFieldException('Undefined type of field: '. $field);
    }

    /**
     * @param string $field
     * @return mixed
     * @throws RequestCollectionFieldException
     */
    protected function getFieldFilter(string $field)
    {
        if (array_key_exists($field, $this->allowedFields)) {
            return $this->allowedFields[$field]['filter'];
        }
        throw new RequestCollectionFieldException('Undefined type of field: '. $field);
    }

    /**
     * @param string $field
     * @return string
     */
    protected function getFieldMapping(string $field) :string
    {
        if (array_key_exists($field, $this->allowedFields)) {
            if (array_key_exists('mapping', $this->allowedFields[$field])) {
                return $this->allowedFields[$field]['mapping'];
            }
        }
        return $field;
    }

    protected function normalizeFields()
    {
        if (!empty($this->allowedFields)) {
            $allowedFields = [];
            $normalizedValue = [
                self::FILTERED => true,
                self::SORTABLE => true,
                'type' => Field::FIELD_OWN,
                'filter' => Field::FILTER_SINGLE,
            ];
            foreach ($this->allowedFields as $key => $value) {
                if (is_string($value)) {
                    $allowedFields[$value] = $normalizedValue;
                }

                if (!is_string($key)) {
                    continue;
                }

                if (is_array($value)) {
                    $allowedFields[$key] = array_merge($normalizedValue, $value);
                } else {
                    $allowedFields[$key] = $normalizedValue;
                }
            }
            $this->allowedFields = $allowedFields;
        }
    }
}
