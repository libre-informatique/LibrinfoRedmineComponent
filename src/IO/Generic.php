<?php

namespace Librinfo\RedmineComponent\IO;

abstract class Generic
{
    /**
     * @var string
     **/
    private $data;
    
    public function __construct(string $data, ?array $options = [])
    {
        $this->data = $data;
    }
    
    public function get(): string
    {
        return $this->data;
    }
    
    public function __toString(): string
    {
        return $this->get();
    }
}
