<?php

namespace App\Repository\Specification;

use Doctrine\ORM\QueryBuilder;
use Happyr\DoctrineSpecification\BaseSpecification;

class ContainsFilter extends BaseSpecification
{
    const CONTAINS = '%%%s%%';
    const STARTS_WITH = '%s%%';

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $value;

    public function __construct(string $field, string $value, ?string $dqlAlias = null)
    {
        $this->field = $field;
        $this->value = $value;
        parent::__construct($dqlAlias);
    }

    public function getFilter(QueryBuilder $qb, $dqlAlias)
    {
        $paramName = $this->getParameterName($qb);
        $qb->setParameter($paramName, $this->formatValue($this->value));

        return 'LOWER(' . sprintf('%s.%s', $dqlAlias, $this->field) . ') LIKE ' . sprintf(':%s', $paramName);
    }

    private function getParameterName(QueryBuilder $qb)
    {
        return sprintf('contains_%d', $qb->getParameters()->count());
    }

    private function formatValue($value)
    {
        if (strlen($value) > 5) {
            $format = self::CONTAINS;
        } else {
            $format = self::STARTS_WITH;
        }
        return sprintf($format, mb_strtolower($value));
    }
}