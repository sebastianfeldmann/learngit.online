<?php

namespace LearnGit;

class Request
{
    private string $method;
    private array $data;

    public function __construct($uri, $method)
    {
        $this->method = $method;
        $this->data  = parse_url($uri);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->data['path'] ?? '';
    }

    public function query(): string
    {
        return $this->data['query'] ?? [];
    }
}
