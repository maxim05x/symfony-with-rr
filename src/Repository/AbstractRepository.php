<?php

namespace App\Repository;

use App\Http\ParamConverter\RequestCollection;
use App\Repository\Specification\FilterCriteria;
use App\Repository\Specification\PaginationCriteria;
use App\Repository\Specification\SortCriteria;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Happyr\DoctrineSpecification\BaseSpecification;
use Happyr\DoctrineSpecification\EntitySpecificationRepository;
use Happyr\DoctrineSpecification\Spec;

abstract class AbstractRepository
{
    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @var EntitySpecificationRepository|ObjectRepository
     */
    protected $repo;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
        $this->repo = $manager->getRepository(static::entityClass());
    }

    /**
     * @param RequestCollection $collection
     * @param BaseSpecification|null $customCriteria
     * @return Paginator|iterable|mixed[]
     */
    public function getList(RequestCollection $collection, BaseSpecification $customCriteria = null)
    {
        $spec = Spec::andX(
            new FilterCriteria($collection->getFilter()),
            new SortCriteria($collection->getSort()),
            $customCriteria
        );

        $pagination = $collection->getPagination();

        if ($pagination->isEnabled()) {
            $spec->andX(new PaginationCriteria($pagination));
            return new Paginator($this->repo->getQuery($spec), false);
        }

        return $this->repo->match($spec);
    }

    /**
     * @return string
     */
    abstract public static function entityClass(): string;
}
