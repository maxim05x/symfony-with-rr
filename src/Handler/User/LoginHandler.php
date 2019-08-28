<?php

namespace App\Handler\User;

use App\Entity\User\User;
use App\Http\Request\Session\LoginRequest;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class LoginHandler
{
    /**
     * @var UserRepository
     */
    private $repository;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var JWTTokenManagerInterface
     */
    private $tokenManager;

    public function __construct(
        UserRepository $repository,
        UserPasswordEncoderInterface $encoder,
        JWTTokenManagerInterface $tokenManager
    ) {
        $this->repository = $repository;
        $this->encoder = $encoder;
        $this->tokenManager = $tokenManager;
    }

    /**
     * @param LoginRequest $request
     * @return User
     */
    public function __invoke(LoginRequest $request)
    {
        if (!$user = $this->repository->findByEmail($request->getEmail())) {
            throw new BadCredentialsException();
        }

        if (!$this->encoder->isPasswordValid($user, $request->getPassword())) {
            throw new BadCredentialsException();
        }

        return $user;
    }

    /**
     * @param User $user
     * @return string
     */
    public function createToken(User $user): string
    {
        return $this->tokenManager->create($user);
    }
}