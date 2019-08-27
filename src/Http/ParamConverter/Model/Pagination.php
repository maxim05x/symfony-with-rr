<?php

namespace App\Http\ParamConverter\Model;

class Pagination
{
    public const DEFAULT_OFFSET = 0;
    public const DEFAULT_LIMIT = 20;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $offset;

    /**
     * @var int
     */
    private $before;

    /**
     * @var bool
     */
    private $disabled = false;

    /**
     * @param int $offset
     * @param int $limit
     * @param int $before
     */
    public function __construct($offset = self::DEFAULT_OFFSET, $limit = self::DEFAULT_LIMIT, $before = 0)
    {
        $this->limit = (int) $limit;
        $this->offset = (int) $offset;
        $this->before = (int) $before;
    }

    public function update($offset = self::DEFAULT_OFFSET, $limit = self::DEFAULT_LIMIT, $before = 0)
    {
        $this->limit = (int) $limit;
        $this->offset = (int) $offset;
        $this->before = (int) $before;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getBefore(): int
    {
        return $this->before;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return !$this->disabled;
    }

    public function disable()
    {
        $this->disabled = true;
    }
}
