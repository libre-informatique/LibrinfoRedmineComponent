<?php

namespace Librinfo\RedmineComponent\Entity;

class Project extends Entity
{
    protected $id;
    protected $name;
    protected $identifier;
    protected $description;
    protected $status;
    protected $createdOn;
    protected $udpatedOn;
    protected $parent;
    
    public function isFulfilled(): bool
    {
        return parent::isFulfilled() && $this->has('identifier');
    }
    
    protected function hydrateValue($value, ?string $name = NULL)
    {
        if ( !isset($name) ) {
            return parent::hydrateValue($value, $name);
        }
        
        if ( $name != 'parent' ) {
            return parent::hydrateValue($value, $name);
        }
        
        if ( !is_array($value) ) {
            return $value;
        }
        
        return Project::create($value);
    }
}
