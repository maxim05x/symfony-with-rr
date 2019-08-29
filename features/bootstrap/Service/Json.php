<?php

namespace Features\Service;

use Exception;
use JsonSchema\SchemaStorage;
use JsonSchema\Uri\UriResolver;
use JsonSchema\Uri\UriRetriever;
use JsonSchema\Validator;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Json
{
    /** @var mixed */
    private $content;

    /** @var PropertyAccessor  */
    private $accessor;

    /**
     * @param string|object|null $content
     * @throws Exception
     */
    public function __construct($content)
    {
        if (is_string($content) || is_null($content)) {
            $this->content = $this->decode((string)$content);
        } elseif (is_object($content)) {
            $this->content = $content;
        } else {
            throw new Exception("Incompatible type");
        }
    }

    /**
     * @param string $expr
     * @return array|mixed|string
     * @throws Exception
     */
    public function getValue(string $expr)
    {
        if (strlen(trim($expr)) === 0) {
            return $this->content;
        }
        if (!$this->accessor instanceof PropertyAccessor) {
            $this->accessor = new PropertyAccessor(false, true);
        }
        return $this->accessor->getValue($this->content, $expr);
    }

    /**
     * @param string $schemaFile
     * @return bool
     * @throws Exception
     */
    public function validate(string $schemaFile): bool
    {
        $validator = new Validator();
        $resolver = new SchemaStorage(new UriRetriever, new UriResolver);

        $schemaContent = $resolver->resolveRef($schemaFile);

        $validator->check($this->content, $schemaContent);

        if (!$validator->isValid()) {
            $msg = "JSON does not validate. Violations:".PHP_EOL;
            foreach ($validator->getErrors() as $error) {
                $msg .= sprintf("  - [%s] %s".PHP_EOL, $error['property'], $error['message']);
            }
            throw new Exception($msg);
        }

        return true;
    }

    /**
     * @param bool $pretty
     * @return string
     */
    public function encode($pretty = true)
    {
        if (true === $pretty) {
            return (string)json_encode($this->content, JSON_PRETTY_PRINT);
        }
        return (string)json_encode($this->content);
    }

    private function decode(string $content)
    {
        $result = json_decode($content);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("The string '$content' is not valid json");
        }
        return $result;
    }
}