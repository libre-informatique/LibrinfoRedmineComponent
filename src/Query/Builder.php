<?php

namespace Librinfo\RedmineComponent\Query;

use Librinfo\RedmineComponent\Exception\QueryException;
use Librinfo\RedmineComponent\Utils\StringConverter;

class Builder
{
    protected $values = [];
    protected $operands = [];
    protected $current;
    
    public function getQuerystring(): string
    {
        $qs = [];
        $sc = new StringConverter;
        
        foreach ( $this->values as $name => $value ) {
            if ( $this->isValueSimple($value) ) {
                $qs[$name] = $value;
                continue;
            }
        
            if ( !array_key_exists($name, $this->operands) ) {
                continue;
            }
            
            $qs['f'][] = $name;                                 // process the filter on $name criteria
            $qs['v'][$name] = $value;                           // on $value
            $qs['op'][$name] = (string)$this->operands[$name];  // with operand $op
        }
        
        $query = http_build_query($qs);
        return $sc->removeArrayIndexesFromQuerystring($query);
    }
    
    public function __toString(): string
    {
        return $this->getQuerystring();
    }
    
    private function isValueSimple($value): bool
    {
        if ( !isset($value) ) {
            return true;
        }
        
        if ( is_array($value) ) {
            return false;
        }
        
        return true;
    }
    
    /**
     * @function addCriterion
     *
     * @param string|null $name  if null, the current criteria is taken
     * @return Builder    $this
     * @throw QueryException     if the used criteria is unavailable in this context
     **/
    public function addCriterion(?string $name = NULL): Builder
    {
        $this->setCurrent($name);
        
        if ( !array_key_exists($this->current, $this->values) ) {
            $this->values[$this->current] = [];
        }
        
        return $this->setCurrent($this->current);
    }
    
    /**
     * @param ?string name
     * @return Build  $this
     * @throw QueryException
     **/
    public function setCurrent(?string $name = NULL): Builder
    {
        if ( $name ) {
            $this->current = $name;
        }
        
        $this->isCurrentExists();
        return $this;
    }
    private function isCurrentExists(): bool
    {
        if ( !$this->current ) {
            throw new QueryException('['.self::class.'] Invalid pointer exception');
        }
        return true;
    }
    public function getCurrent(): string
    {
        $this->isCurrentExists();
        return $this->current;
    }
    
    public function addValue(?string $value, ?string $criteria = NULL): Builder
    {
        $this->setCurrent($criteria);
        $this->values[$this->current][] = $value;
        if ( !array_key_exists($this->current, $this->operands) ) {
            $this->operands[$this->current] = new Operand('equal');
        }
        return $this;
    }
    
    public function setValue(?string $value, ?string $criteria = NULL): Builder
    {
        $this->setCurrent($criteria);
        $this->values[$this->current] = $value;
        if ( !array_key_exists($this->current, $this->operands) ) {
            $this->operands[$this->current] = new Operand('equal');
        }
        return $this;
    }
    
    public function setValues(array $values, ?string $criteria = NULL): Builder
    {
        $this->setCurrent($criteria);
        $this->values[$this->current] = $values;
        if ( !array_key_exists($this->current, $this->operands) ) {
            $this->operands[$this->current] = new Operand('equal');
        }
        return $this;
    }
    
    /**
     * @function getValue
     * 
     * @param  string|null  $criteria or null for current
     * @return mixed
     **/
    public function getValues(?string $criteria = NULL)
    {
        $this->setCurrent($criteria);
        return $this->values[$this->current];
    }
    
    public function setOperand(Operand $op, ?string $criteria = NULL): Builder
    {
        $this->setCurrent($criteria);
        $this->operands[$this->current] = $op;
        return $this;
    }
    public function getOperand(?string $criteria = NULL): Operand
    {
        $this->setCurrent($criteria);
        return $this->operands[$this->current];
    }
    public function hasOperand(?string $criteria = NULL): bool
    {
        $this->setCurrent($criteria);
        return array_key_exists($this->current, $this->operands);
    }
}
