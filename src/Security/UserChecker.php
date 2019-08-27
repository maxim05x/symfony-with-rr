<?php

namespace App\Security;

use App\Entity\User\User;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        // TODO: Implement checkPreAuth() method.
    }

    /**
     * @param UserInterface|User $user
     */
    public function checkPostAuth(UserInterface $user)
    {
        if ($user instanceof User && !$user->isEnabled()) {
            throw new AccessDeniedException();
        }
    }
}
