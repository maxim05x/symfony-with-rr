<?php

namespace App\Http\ParamConverter;

use App\Http\ParamConverter\Exception\RequestObjectPayloadException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestObjectConverter implements ParamConverterInterface
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @param ParamConverter $configuration
     * @return bool|void
     * @throws RequestObjectPayloadException
     */
    public function apply(Request $request, ParamConverter $configuration)
    {
        $class = $configuration->getClass();
        /** @var RequestObject $object */
        $object = new $class;

        $object->setPayload(
            array_merge(
                $request->request->all(),
                $request->files->all()
            )
        );

        $errors = $this->validator->validate(
            $object->all(),
            $object->rules()
        );

        if (count($errors) !== 0) {
            throw new RequestObjectPayloadException($object, $errors);
        }

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
        return is_subclass_of($configuration->getClass(), RequestObject::class);
    }
}
