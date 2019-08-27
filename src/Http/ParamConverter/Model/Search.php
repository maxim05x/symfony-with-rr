<?php

namespace App\Http\ParamConverter\Model;

class Search
{
    private $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function update($query)
    {
        $this->query = $query;
    }

    public function getQuery()
    {
        return $this->query;
    }
}