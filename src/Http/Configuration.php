<?php

namespace Librinfo\RedmineComponent\Http;

class Configuration
{
    private $baseUrl;
    private $key;
    
    public function __construct(string $baseUrl, string $key)
    {
        $this->baseUrl = $baseUrl;
        $this->key = $key;
    }
    
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
    public function getKey(): string
    {
        return $this->key;
    }
}
