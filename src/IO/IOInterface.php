<?php

namespace Librinfo\RedmineComponent\IO;

interface IOInterface
{
    public function __construct(string $data, ?array $options = []);
    
    public function get(): string;
    
    public function __toString(): string;
    
    public function toArray(): array;
}
