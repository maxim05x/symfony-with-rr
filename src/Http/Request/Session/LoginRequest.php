<?php

namespace App\Http\Request\Session;

use App\Http\ParamConverter\RequestObject;
use Symfony\Component\Validator\Constraints as Assert;

class LoginRequest extends RequestObject
{
    public function rules()
    {
        return new Assert\Collection([
            'allowExtraFields' => false,
            'fields' => [
                'email' => [
                    new Assert\Type('string'),
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
                'password' => [
                    new Assert\Type('string'),
                    new Assert\NotBlank(),
                ]
            ]
        ]);
    }

    public function getEmail(): string
    {
        return $this->get('email');
    }

    public function getPassword(): string
    {
        return $this->get('password');
    }
}
