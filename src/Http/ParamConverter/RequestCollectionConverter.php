<?php

namespace App\Http\ParamConverter;

use App\Http\ParamConverter\Model\Pagination;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class RequestCollectionConverter implements ParamConverterInterface
{
    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @return bool|void
     * @throws Exception\RequestCollectionFieldException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = $configuration->getClass();
        /** @var RequestCollection $object */
        $object = new $class;

        $filterParams = $request->query->get('filter', []);
        foreach ($filterParams as $field => $value) {
            $object->addFilter($field, $value);
        }

        $sortParams = $request->query->get('sort', []);
        foreach ($sortParams as $field => $direction) {
            $object->addSort($field, $direction);
        }

        $page = $request->query->get('page', []);
        $object->updatePagination(
            $page['offset'] ?? Pagination::DEFAULT_OFFSET,
            $page['limit'] ?? Pagination::DEFAULT_LIMIT,
            $page['before'] ?? time()
        );

        $q = $request->query->get('q');
        $object->updateSearch($q);

        $request->attributes->set(
            $configuration->getName(),
            $object
        );
    }

    /**
     * @param ParamConverter $configuration
     * @return bool
     */
    public function supports(ParamConverter $configuration)
    {
        return $configuration->getClass() === RequestCollection::class
            || is_subclass_of($configuration->getClass(), RequestCollection::class);
    }
}
