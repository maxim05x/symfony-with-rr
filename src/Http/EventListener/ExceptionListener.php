<?php

namespace App\Http\EventListener;

use App\Http\ParamConverter\Exception\RequestObjectPayloadException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Contracts\Translation\TranslatorInterface;

class ExceptionListener
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();

        if ($exception instanceof RequestObjectPayloadException) {
            $errors= $exception->getErrors();
            $event->setResponse(new JsonResponse(
                [
                    'status_code' => 422,
                    'message' => 'Invalid data in request body',
                    'errors' => array_map(function (ConstraintViolation $violation) {
                        return [
                            'field' => trim(str_replace('][', '.', $violation->getPropertyPath()), '[]'),
                            'message' => $violation->getMessage(),
                        ];
                    }, iterator_to_array($errors)),
                ],
                422
            ));
        } else {
            $contentType = $request->headers->get('Content-Type');

            if (in_array('application/json', explode(';', $contentType))) {
                $message = $exception->getMessage();
                $code = $exception->getCode();

                $event->setResponse(new JsonResponse(
                    [
                        'success' => false,
                        'error' => $this->translator->trans($message)
                    ],
                    $code
                ));
            }
        }
    }
}
