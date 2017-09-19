<?php

namespace Librinfo\RedmineComponent\Entity;

use Librinfo\RedmineComponent\Exception\DataInjectionException;
use Librinfo\RedmineComponent\Utils\StringConverter;

abstract class Entity implements EntityInterface
{
    /**
     * @var bool
     **/
    private $debug;
    
    public function __construct(array $data, ?bool $debug = false)
    {
        $this->debug = $debug;
        $this->hydratePropertiesFromArray($data);
    }
    
    public function get(string $property): ?string
    {
        $rc = new \ReflectionClass($this);
        if ( !$rc->hasProperty($property) ) {
            $this->log(sprintf('Property %s is not available for User', $property));
            return NULL;
        }
        
        return $this->$property;
    }
    
    private function hydratePropertiesFromArray(array $data): void
    {
        $rc = new \ReflectionClass($this);
        foreach ( $data as $name => $value ) {
            $converter = new StringConverter;
            $name = $converter->fromSnakeCaseToCamelCase($name);
            if ( !$rc->hasProperty($name) ) {
                $this->log(sprintf('Property %s is not available for User', $name));
                continue;
            }
            
            $this->$name = $value;
        }
    }
    
    public function toArray(bool $deep = false): array
    {
        $rc = new \ReflectionClass($this);
        $data = [];
        
        foreach ( $rc->getProperties() as $prop ) {
            $field = $prop->getName();
            
            if ( $deep && is_object($this->$field) && method_exists($this->$field, 'toArray') ) {
                $data[$field] = $this->$field->toArray($deep);
                continue;
            }
            
            $data[$field] = $this->$field;
        }
        
        return $data;
    }
    
    protected function isInDebugMode(): bool
    {
        return $this->debug;
    }
    
    protected function log(string $str): void
    {
        if ( $this->isInDebugMode() ) {
            error_log($str);
        }
    }
}
