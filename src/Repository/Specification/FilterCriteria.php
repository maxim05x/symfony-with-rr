<?php

namespace App\Repository\Specification;

use App\Http\ParamConverter\Model\Field;
use App\Http\ParamConverter\Model\Filter;
use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class FilterCriteria extends BaseSpecification
{
    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var string|null
     */
    private $dqlAlias;

    public static $joinedFields = [];

    public function __construct(Filter $filter, ?string $dqlAlias = null)
    {
        $this->filter = $filter;
        $this->dqlAlias = $dqlAlias;
        parent::__construct($dqlAlias);
    }

    protected function getSpec()
    {
        $spec = Spec::andX();

        foreach ($this->filter->getAll() as $filter) {
            $dqlAlias = $this->dqlAlias;
            if ($filter->getType() === Field::FIELD_RELATED) {
                list ($join, $field) = explode('.', $filter->getField(), 2);
                $dqlAlias = $join.'_f';

                if (!in_array($dqlAlias, self::$joinedFields)) {
                    $spec->andX(Spec::join($join, $dqlAlias, $this->dqlAlias));
                }
            } else {
                $field = $filter->getField();
            }

            if ($filter->getFilter() === Field::FILTER_MULTIPLE) {
                $value = explode(',', (string)$filter->getValue());
                $spec->andX(Spec::in($field, $value, $dqlAlias));
            } else {
                $value = $filter->getValue();
                if ($filter->getFilter() === Field::FILTER_CONTAINS) {
                    $spec->andX(new ContainsFilter($field, $value, $dqlAlias));
                } else {
                    $spec->andX(Spec::eq($field, $value, $dqlAlias));
                }
            }
        }

        return $spec;
    }
}
