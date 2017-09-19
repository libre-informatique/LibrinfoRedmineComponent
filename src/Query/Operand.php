<?php

namespace Librinfo\RedmineComponent\Query;

use Librinfo\RedmineComponent\Exception\QueryException;

class Operand
{
    const ACCEPTABLE = [
        'equal' => '=',
        'differnt' => '!=',
        'noneof' => '!*',
        'all' => '*',
        'lessthan' => '<=',
        'morethan' => '>=',
        'between' => '><',
    ];
    
    private $humanSide;
    private $serverSide;
    
    public function __construct(string $operand)
    {
        $this->humanSide = $operand;
        
        if ( !isset(self::ACCEPTABLE[$operand]) ) {
            throw new QueryException(sprintf('[%s] Invalid operand %s', self::class, $operand));
        }
        
        $this->serverSide = self::ACCEPTABLE[$operand];
    }
    
    public function __toString(): string
    {
        return $this->serverSide;
    }
    
    public function getOperand(): string
    {
        return ['server' => $this->serverSide, 'human' => $this->humanSide];
    }
}
