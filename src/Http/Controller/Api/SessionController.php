<?php

namespace App\Http\Controller\Api;

use App\Handler\User\LoginHandler;
use App\Http\Controller\AppController;
use App\Http\Request\Session\LoginRequest;
use App\Http\Transformer\User\SessionTransformer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/session")
 */
class SessionController extends AppController
{
    /**
     * @Route("/token", methods={"POST"})
     *
     * @param LoginRequest $request
     * @param LoginHandler $handler
     * @param SessionTransformer $transformer
     * @return JsonResponse
     */
    public function token(LoginRequest $request, LoginHandler $handler, SessionTransformer $transformer)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_ANONYMOUSLY');

        $user = $handler($request);
        $transformer->withToken($handler->createToken($user));

        return $this->resource($user, $transformer)->asResponse();
    }
}
