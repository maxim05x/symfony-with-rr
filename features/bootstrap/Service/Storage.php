<?php declare(strict_types=1);

namespace Features\Service;

class Storage
{
    protected $parameters;

    public function __construct(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function set(string $key, $value)
    {
        $this->parameters[$key] = $value;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->parameters);
    }

    public function get(string $key, $default = null)
    {
        return $this->has($key) ? $this->parameters[$key] : $default;
    }

    public function replace(array $parameters = [])
    {
        $this->parameters = $parameters;
    }

    public function all(): array
    {
        return $this->parameters;
    }

    /**
     * Replace in source string all {{key}} to storage value
     * @param string|null $source
     * @return string
     */
    public function formatKeyAsValue(?string $source): string
    {
        return (string)preg_replace_callback(
            '/({{[a-zA-Z0-9_\-]+}})/',
            function(array $matches) {
                $val = (string)current($matches);
                $key = trim($val, '{}');
                return array_key_exists($key, $this->parameters)
                    ? $this->parameters[$key]
                    : (array_key_exists($val, $this->parameters)
                        ? $this->parameters[$val]
                        : $val);
            },
            (string)$source
        );
    }
}