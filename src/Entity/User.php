<?php

namespace Librinfo\RedmineComponent\Entity;

use Librinfo\RedmineComponent\Exception\DataInjectionException;
use Librinfo\RedmineComponent\Utils\StringConverter;

class User
{
    private $id;
    private $login;
    private $firstname;
    private $lastname;
    private $mail;
    private $createdOn;
    private $lastLoginOn;
    
    public function __construct(array $data)
    {
        $rc = new \ReflectionClass($this);
        foreach ( $data as $name => $value ) {
            $converter = new StringConverter;
            $name = $converter->fromSnakeCaseToCamelCase($name);
            if ( !$rc->hasProperty($name) ) {
                throw new DataInjectionException(sprintf('Property %s is not available for User', $name));
            }
            
            $this->$name = $value;
        }
    }
    
    public function __toString(): string
    {
        return $this->firstname.' '.$this->lastname;
    }
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'mail' => $this->mail,
            'createdOn' => $this->createdOn,
            'lastLoginOn' => $this->lastLoginOn,
        ];
    }
}
