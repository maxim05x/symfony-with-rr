<?php

namespace Features\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\Security\Core\User\UserInterface;

class Auth
{
    /** @var JWTManager */
    private $jwtManager;

    public function __construct(JWTManager $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    /**
     * @param UserInterface $user
     * @return string
     */
    public function createToken(UserInterface $user)
    {
        return $this->jwtManager->create($user);
    }
}
