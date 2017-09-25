<?php

namespace Librinfo\RedmineComponent\Entity;

use Librinfo\RedmineComponent\Exception\DataInjectionException;
use Librinfo\RedmineComponent\Utils\StringConverter;
use Librinfo\RedmineComponent\Exception\PrerequisitesException;
use Librinfo\RedmineComponent\Core\Context;

abstract class Entity implements EntityInterface
{
    /**
     * @var bool
     **/
    private $debug;
    
    public static function create(array $data, ?bool $debug = false)
    {
        $catalog = Context::getInstance()->getCatalog();
        
        // from data
        $entity = new static($debug);
        $entity->hydrate($data);
        
        // from catalog, amended by data
        if ( $entity->isFulfilled() && $catalog->has($entity) ) {
            $entity = $catalog->get($entity);
            $entity->hydrate($data);
        }
        
        try {
            $catalog->add($entity);
        } catch ( PrimaryKeyException $e ) {
            $this->log($e->getMessage());
        }
        
        return $entity;
    }
    
    private function __construct(?bool $debug = false)
    {
        $this->debug = $debug;
    }
    
    /**
     * Function that returns the content of a property
     *
     * @param string  $property
     * @return mixed  Depending on the data stored...
     */
    public function get(string $property)
    {
        if ( !$this->hasProperty($property) ) {
            return NULL;
        }
        return $this->$property;
    }
    
    /**
     * Function that checks if a property has been defined
     *
     * @param string  $property
     * @param bool
     */
    public function has(string $property): bool
    {
        if ( !$this->hasProperty($property) ) {
            return false;
        }
        return isset($this->$property);
    }
    
    /**
     * Function that sets the content to a property of the current object
     *
     * @param string  $property
     * @param mixed   $value
     */
    public function set(string $property, $value): void
    {
        if ( !$this->hasProperty($property) ) {
            return;
        }
        $this->$property = $value;
        return;
    }
    
    public static function getIdentifier(): string
    {
        return 'id';
    }
    
    public function getIdentifierValue()
    {
        if ( !$this->hasProperty('id') ) {
            throw new PrimaryKeyException('[Entity] No primary key defined');
        }
        
        return $this->get($this->getIdentifier());
    }
    
    protected function hasProperty(string $property)
    {
        $rc = new \ReflectionClass($this);
        if ( !$rc->hasProperty($property) ) {
            $this->log(sprintf('Property %s is not available for User', $property));
            return false;
        }
        
        return true;
    }
    
    public function hydrate(array $data): void
    {
        $rc = new \ReflectionClass($this);
        foreach ( $data as $name => $value ) {
            $converter = new StringConverter;
            $name = $converter->fromSnakeCaseToCamelCase($name);
            if ( !$rc->hasProperty($name) ) {
                $this->log(sprintf('Property %s is not available for User', $name));
                continue;
            }
            
            $this->$name = $this->hydrateValue($value, $name);
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
    
    public function __toString(): string
    {
        if ( !isset($this->name) ) {
            $this->log('Property "name" is not defined and no thing more specific has been defined neither.');
            return '*RedmineEntity*';
        }
        
        return $this->name;
    }

    public function isFulfilled(): bool
    {
        return $this->has($this->getIdentifier());
    }
    
    /**
     * Function hydrateValue
     * Entity::hydrateValue() is dummy, extend it if needed
     *
     * @param mixed        $value
     * @param string|null  $name
     * @return mixed
     *
     */
    protected function hydrateValue($value, ?string $name = NULL)
    {
        return $value;
    }
    
    protected function isElligibleToSpecialHydration($value, ?string $name): bool
    {
        if ( !isset($name) ) {
            return false;
        }
        
        if ( !is_array($value) ) {
            return false;
        }
        
        return true;
    }
    
    protected function isInDebugMode(): bool
    {
        return $this->debug;
    }
    
    protected function log(string $str): void
    {
        if ( $this->isInDebugMode() ) {
            error_log(sprintf('[%s] %s', static::class, $str));
        }
    }
}
