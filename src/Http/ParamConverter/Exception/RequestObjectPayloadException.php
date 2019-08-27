<?php

namespace App\Http\ParamConverter\Exception;

use App\Http\ParamConverter\RequestObject;
use Exception;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestObjectPayloadException extends Exception
{
    /**
     * @var RequestObject
     */
    private $requestObject;
    /**
     * @var ConstraintViolationListInterface
     */
    private $errors;

    public function __construct(RequestObject $requestObject, ConstraintViolationListInterface $errors)
    {
        $this->requestObject = $requestObject;
        $this->errors = $errors;
        parent::__construct();
    }

    /**
     * @return RequestObject
     */
    public function getRequestObject(): RequestObject
    {
        return $this->requestObject;
    }

    /**
     * @return ConstraintViolationListInterface
     */
    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }
}
