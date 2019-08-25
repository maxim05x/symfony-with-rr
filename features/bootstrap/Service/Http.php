<?php

namespace Features\Service;

use Symfony\Component\HttpFoundation\Request;

class Http
{
    /** @var Storage  */
    private $storage;

    /** @var string  */
    private $baseUrl;

    /** @var string */
    private $authKey;

    /**
     * @param Storage $storage
     * @param string $baseUrl
     * @param string $authKey
     */
    public function __construct(Storage $storage, string $baseUrl = '', string $authKey = 'HTTP_X_Authorization')
    {
        $this->storage = $storage;
        $this->baseUrl = $baseUrl;
        $this->authKey = $authKey;
    }

    public function makeRequest($method, $url, array $headers = [], $data = null, $files = []): Request
    {
        $body = null;
        if (!in_array($method, ['GET', 'DELETE'])) {
            $body = $this->storage->formatKeyAsValue(
                $this->buildRequestBody($data)
            );
        }
        $headers = $this->prepareHeaders($headers);

        return Request::create(
            sprintf('%s/%s', rtrim($this->baseUrl, '/'), ltrim($this->storage->formatKeyAsValue($url), '/')),
            $method,
            [], [], $files,
            $headers,
            $body
        );
    }

    private function buildRequestBody($body): ?string
    {
        if (is_string($body)) {
            return $body;
        }
        if(is_array($body)) {
            return (new Json($body))->encode(false);
        }
        return null;
    }

    private function prepareHeaders(array $headers): array
    {
        if (!array_key_exists('CONTENT_TYPE', $headers)) {
            $headers['CONTENT_TYPE'] = 'application/json';
        }
        if ($this->storage->has('token')) {
            $headers[$this->authKey] = sprintf('Bearer %s', $this->storage->get('token'));
        } elseif ($this->storage->has('{{token}}')) {
            $headers[$this->authKey] = sprintf('Bearer %s', $this->storage->get('{{token}}'));
        }
        return $headers;
    }
}