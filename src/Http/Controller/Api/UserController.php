<?php

namespace App\Http\Controller\Api;

use App\Http\Controller\AppController;
use App\Http\Request\User\UserListRequest;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1/users")
 */
class UserController extends AppController
{
    /**
     * @Route("", methods={"GET"})
     *
     * @param UserListRequest $request
     * @param UserRepository $repository
     * @return JsonResponse
     */
    public function listAction(UserListRequest $request, UserRepository $repository)
    {
//        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->resource($repository->getList($request))->asResponse();
    }
}
