<?php

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Client as RedmineClient;
use Librinfo\RedmineComponent\Http\Configuration;
use Librinfo\RedmineComponent\Entity\Entity;
use Librinfo\RedmineComponent\Collection\Collection;
use Librinfo\RedmineComponent\Exception\PrerequisitesException;

abstract class Generic extends RedmineClient
{
    /**
     * @var string base class to play with
     */
    private $entity;
    
    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);
        $this->entity = $this->getEntityClass();
    }
    
    protected abstract function getEntityClass(): string;
    
    /**
     * @function getAll  get all data from a pagined request
     *
     * @param int $max     the global "max" to retrieve data (optional), -1 means infinite
     * @param int $page    the number of results per page, has no effet on the result, but on perfs, (optional)
     */
    public function getAll(int $max = -1, int $page = 100): Collection
    {
        $this->checkPrerequisites();
        
        $this->getClient()->setLimit(100);
        $this->getClient()->setQuerystring($this->getBuilder());
        
        $entities = new Collection($this->entity);
        foreach ( $this->getClient()->getFullData($max) as $raw ) {
            $entities[] = new $this->entity($raw);
        }
        
        return $entities;
    }
    
    /**
     * @function getOne  get back one entity
     *
     * @var @id  int  id of the targeted object
     * @return Entity|NULL
     */
    public function getOne(int $id): ?Entity
    {
        $this->checkPrerequisites();
        
        $this->getClient()->setRoute($this->getRoute().'/'.$id);
        
        $rc = new \ReflectionClass($this->entity);
        $data = $this->getClient()->getData()[strtolower($rc->getShortName())];
        
        if ( $data ) {
            return new $this->entity($data);
        }
        
        return NULL;
    }
    
    protected function checkPrerequisites(): bool
    {
        parent::checkPrerequisites();
        if ( !$this->entity ) {
            throw new PrerequisitesException('You need to define an entity prior to any request');
        }
        
        return true;
    }
}
