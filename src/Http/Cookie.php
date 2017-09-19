<?php

namespace Librinfo\RedmineComponent\Http;

class Cookie
{
    private $name;
    private $value;
    
    public function __construct(string $name, ?string $value = NULL)
    {
        $this->name = $name;
        $this->value = $value;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    public function getValue(): string
    {
        return $this->value;
    }
    
    public function __toString(): string
    {
        return sprintf('%s=%s', $this->name, $this->value);
    }
}
