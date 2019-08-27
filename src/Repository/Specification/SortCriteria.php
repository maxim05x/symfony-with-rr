<?php

namespace App\Repository\Specification;

use App\Http\ParamConverter\Model\Field;
use App\Http\ParamConverter\Model\Sort;
use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\BaseSpecification;

class SortCriteria extends BaseSpecification
{
    /**
     * @var Sort
     */
    private $sort;

    public static $joinedFields = [];

    public function __construct(Sort $sort, $dqlAlias = null)
    {
        $this->sort = $sort;
        parent::__construct($dqlAlias);
    }

    public function modify(QueryBuilder $qb, $dqlAlias)
    {
        foreach ($this->sort->getAll() as $sort) {
            if ($sort->getType() === Field::FIELD_RELATED) {
                list ($join, $field) = explode('.', $sort->getField(), 2);

                if (!in_array($join, self::$joinedFields)) {
                    $qb->leftJoin("$dqlAlias.$join", $join);
                    self::$joinedFields[] = $join;
                }

                $qb->addOrderBy($join.'.'.$field, $sort->getValue());

            } else {
                $qb->addOrderBy($dqlAlias.'.'.$sort->getField(), $sort->getValue());
            }
        }

        parent::modify($qb, $dqlAlias);
    }
}
