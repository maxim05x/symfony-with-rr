<?php

namespace App\Http\Request\User;

use App\Http\ParamConverter\Model\Field;
use App\Http\ParamConverter\RequestCollection;

class UserListRequest extends RequestCollection
{
    protected $allowedFields = [
        'email' => [
            'filtered' => true,
            'sortable' => true,
            'type' => Field::FIELD_OWN,
            'filter' => Field::FILTER_SINGLE,
        ]
    ];
}
