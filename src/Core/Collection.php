<?php

namespace Librinfo\RedmineComponent\Core;

use Librinfo\RedmineComponent\Entity\Entity;

class Collection implements \ArrayAccess, \Iterator
{
    /**
     * @var string
     **/
    protected $type = NULL;
    
    /**
     * @var string
     **/
    private $data;
    
    /**
     * @var integer
     **/
    private $intern;
    
    /**
     * @function constructor
     *
     * @param $type  Object|string|null
     **/
    public function __construct($type)
    {
        if ( is_object($type) ) {
            $this->type = get_class($type);
            return;
        }
        
        $this->type = $type;
        $this->data = [];
        $this->intern = 0;
    }
    
    private function keys(?int $key = NULL): array
    {
        return array_keys($this->data);
    }
    
    public function key()
    {
        if ( count($this->data) == 0 ) {
            return NULL;
        }
        
        $keys = $this->keys();
        
        if ( !isset($keys[$this->intern]) ) {
            return NULL;
        }
        
        return $keys[$this->intern];
    }
    public function valid(): bool
    {
        return isset($this->keys()[$this->intern]);
    }
    public function current()
    {
        return $this->data[$this->key()];
    }
    public function next(): void
    {
        $keys = $this->keys();
        
        if ( isset($keys[$this->intern]) ) {
            $this->intern++;
            return;
        }
        
        $this->rewind();
    }
    public function rewind(): void
    {
        $this->intern = 0;
    }
    
    protected function isValueValid($value): bool
    {
        if ( !isset($this->type) ) {
            return true;
        }
        
        return $value instanceof $this->type;
    }
    
    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->data);
    }
    
    public function offsetGet($offset)
    {
        if ( !$this->offsetExists($offset) ) {
            return NULL;
        }
        return $this->data[$offset];
    }
    public function offsetSet($offset, $value): void
    {
        if ( !isset($offset) ) {
            $this->data[] = $value;
            return;
        }
        
        $this->data[$offset] = $value;
    }
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }
    
    public function getData(bool $deep = false): array
    {
        if ( !$deep ) {
            return $this->data;
        }
        
        $r = [];
        foreach ( $this->data as $obj ) {
           $r[] = $obj->toArray($deep);
        }
        
        return $r;
    }
    public function toArray(bool $deep = false): array
    {
        return $this->getData($deep);
    }
    
    public function getRandom(): Entity
    {
        $data = $this->data;
        shuffle($data);
        
        return $data[0];
    }
}
