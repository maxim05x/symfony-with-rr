<?php

namespace App\Repository\Specification;

use App\Http\ParamConverter\Model\Pagination;
use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\Spec;

class PaginationCriteria extends BaseSpecification
{
    /**
     * @var Pagination
     */
    private $pagination;

    public function __construct(Pagination $pagination, $dqlAlias = null)
    {
        $this->pagination = $pagination;
        parent::__construct($dqlAlias);
    }

    protected function getSpec()
    {
        return Spec::andX(
            Spec::offset($this->pagination->getOffset()),
            Spec::limit($this->pagination->getLimit())
        );
    }
}
