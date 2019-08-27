<?php

namespace App\Http\Service\Model;

use App\Http\ParamConverter\Model\Pagination;
use Doctrine\ORM\Tools\Pagination\Paginator;

class Collection extends AbstractResource
{
    /**
     * @var Pagination|null
     */
    private $pagination;

    public function doTransform(): array
    {
        if ($this->data instanceof Paginator) {
            $items = $this->data->getIterator();
            $total = $this->data->count();
        } else {
            $items = $this->data;
            $total = count($this->data);
        }

        $data = [
            'data' => [],
        ];

        foreach ($items as $value) {
            $data['data'][] = (new Item($value, $this->transformer))->doTransform();
        }

        if ($this->pagination && $this->pagination->isEnabled()) {
            $data['meta'] = [
                'pagination' => [
                    'offset' => $this->pagination->getOffset(),
                    'limit' => $this->pagination->getLimit(),
                    'total' => $total,
                ]
            ];
        }

        return $data;
    }

    public function setPagination(?Pagination $pagination = null)
    {
        $this->pagination = $pagination;
    }
}
