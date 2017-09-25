<?php

namespace Librinfo\RedmineComponent\Core;

class Catalog
{
    /**
     * @var array  of Collection
     **/
    private $data = [];
    
    public function get($mixed, $id = NULL)
    {
        $id = $mixed instanceof IdentifiableInterface ? $mixed->getIdentifierValue() : $id;
        $catalog = $this->findCatalog($mixed);
        
        if ( !isset($this->data[$catalog]) ) {
            return NULL;
        }
        
        if ( !isset($this->data[$catalog][$id]) ) {
            return NULL;
        }
        
        return $this->data[$catalog][$id];
    }
    
    public function add($mixed, $id = NULL, $value = NULL): void
    {
        $id = $mixed instanceof IdentifiableInterface ? $mixed->getIdentifierValue() : $id;
        $catalog = $this->findCatalog($mixed); $value = !isset($value) ? $mixed : $value;
        
        if ( !isset($this->data[$catalog]) ) {
            $this->data[$catalog] = new Collection($catalog);
        }
        
        if ( !isset($id) ) {
            return;
        }
        
        $this->data[$catalog][$id] = $value;
    }
    
    public function has($mixed, $id = NULL): bool
    {
        $id = $mixed instanceof IdentifiableInterface ? $mixed->getIdentifierValue() : $id;
        $catalog = $this->findCatalog($mixed);
        
        if ( !isset($this->data[$catalog]) ) {
            return false;
        }
        
        return isset($this->data[$catalog][$id]);
    }
    
    public function getCatalogNames(): array
    {
        return array_keys($this->data);
    }
    
    public function getCatalogIds($mixed): array
    {
        $catalog = $this->getCatalogFromVar($mixed);
        
        if ( !isset($this->data[$catalog]) ) {
            return [];
        }
        
        return array_keys($this->data[$catalog]);
    }
    
    public function getFullCatalog($mixed): Collection
    {
        $catalog = $this->findCatalog($mixed);
        
        if ( !isset($this->data[$catalog]) ) {
            return new Collection($catalog);
        }
        
        return $this->data[$catalog];
    }
    
    private function findCatalog($var): string
    {
        if ( !is_object($var) ) {
            return $var;
        }
        
        $rc = new \ReflectionClass($var);
        return $rc->getName();
    }
}
