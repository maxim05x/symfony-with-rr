<?php

namespace App\Http\EventListener;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class BodyListener
{
    /**
     * @var Serializer|SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $contentType = $request->headers->get('Content-Type');

        if ($this->isDecodeable($request)) {
            $format = null === $contentType
                ? $request->getRequestFormat()
                : $request->getFormat($contentType);

            $content = $request->getContent();

            if (!empty($content) && $this->serializer->supportsDecoding($format)) {
                $data = $this->serializer->decode($content, $format);

                if (is_array($data)) {
                    $request->request = new ParameterBag($data);
                } else {
                    throw new BadRequestHttpException('Invalid '.$format.' message received');
                }
            }
        }
    }

    /**
     * Check if we should try to decode the body.
     *
     * @param Request $request
     * @return bool
     */
    private function isDecodeable(Request $request)
    {
        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            return false;
        }

        return !$this->isFormRequest($request);
    }

    /**
     * Check if the content type indicates a form submission.
     *
     * @param Request $request
     * @return bool
     */
    private function isFormRequest(Request $request)
    {
        $contentTypeParts = explode(';', $request->headers->get('Content-Type'));

        if (isset($contentTypeParts[0])) {
            return in_array($contentTypeParts[0], ['multipart/form-data', 'application/x-www-form-urlencoded']);
        }

        return false;
    }
}
