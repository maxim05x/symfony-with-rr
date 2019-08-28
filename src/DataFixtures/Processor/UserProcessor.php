<?php

namespace App\DataFixtures\Processor;

use App\Entity\User\User;
use Fidry\AliceDataFixtures\ProcessorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserProcessor implements ProcessorInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function preProcess(string $id, $object): void
    {
        if ($object instanceof User) {
            $object->updatePassword($this->passwordEncoder->encodePassword($object, $object->getPassword()));
        }
    }

    public function postProcess(string $id, $object): void
    {
    }
}
