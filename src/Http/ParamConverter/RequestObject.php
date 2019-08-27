<?php

namespace App\Http\ParamConverter;


abstract class RequestObject
{
    private $payload = [];

    public function setPayload(array $payload = [])
    {
        $this->payload = $payload;
    }

    /**
     * @return null
     */
    public function rules()
    {
        return null;
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->payload[$name] : $default;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name): bool
    {
        return array_key_exists($name, $this->payload);
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->payload;
    }
}
