<?php

namespace App\Http\ParamConverter\Exception;

use Exception;

class RequestCollectionFieldException extends Exception
{
    public function __construct($message = "")
    {
        parent::__construct($message, 422);
    }
}
