<?php

namespace App\Http\Transformer\User;

use App\Entity\ModelInterface;
use App\Http\Transformer\DefaultTransformer;

class SessionTransformer extends DefaultTransformer
{
    /**
     * @var string
     */
    private $token;

    /**
     * @param string $token
     * @return SessionTransformer
     */
    public function withToken(string $token): self
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @param ModelInterface $model
     * @return array
     */
    public function transform(ModelInterface $model): array
    {
        return array_merge(
            [
                'token' => $this->token,
            ],
            parent::transform($model)
        );
    }
}