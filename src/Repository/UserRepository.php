<?php

namespace App\Repository;

use App\Entity\User\User;

class UserRepository extends AbstractRepository
{
    public static function entityClass(): string
    {
        return User::class;
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return $this->repo->findOneBy(['email' => $email]);
    }
}