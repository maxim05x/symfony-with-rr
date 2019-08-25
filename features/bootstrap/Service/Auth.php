<?php

namespace Features\Service;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\Security\Core\User\UserInterface;

class Auth
{
    /** @var JWTManager */
    private $jwtManager;

    /** @var Storage */
    private $storage;

    public function __construct(JWTManager $jwtManager, Storage $storage)
    {
        $this->jwtManager = $jwtManager;
        $this->storage = $storage;
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
