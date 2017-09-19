<?php

namespace Librinfo\RedmineComponent\Entity;

class Group extends Entity
{
    protected $id;
    protected $name;
    
    public function __toString(): string
    {
        return $this->name;
    }
}
