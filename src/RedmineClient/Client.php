<?php

/*
 * This file is part of the Blast Project package.
 *
 * Copyright (C) 2015-2017 Libre Informatique
 * Copyright (C) 2015-2017 Baptiste LARVOL-SIMON <baptiste.larvol.simon@libre-informatique.fr>
 *
 * This file is licenced under the GNU LGPL v3.
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Librinfo\RedmineComponent\RedmineClient;

use Librinfo\RedmineComponent\Http\Client as HttpClient;
use Librinfo\RedmineComponent\Http\Configuration;
use Librinfo\RedmineComponent\Query\Builder;
use Librinfo\RedmineComponent\Exception\PrerequisitesException;

abstract class Client
{
    /**
     * @var HttpClient
     */
    private $client;
    
    /**
     * @var array
     */
    private $parameters = [];
    
    /**
     * @var string
     */
    private $route;
    
    /**
     * @var Builder
     **/
    private $builder;
    
    /**
     * @return Configuration
     **/
    public function __construct(Configuration $configuration)
    {
        $this->route = $this->getRoute();
        
        $this->client = new HttpClient($configuration);
        $this->client->setRoute($this->route);
        $this->client->setMethod();
        
        $this->builder = new Builder;
        $this->initParameters();
    }
    
    protected abstract function initParameters(): void;
    protected abstract function getRoute(): string;
    
    public function getBuilder(): Builder
    {
        return $this->builder;
    }
    protected function setBuilder(Builder $builder): void
    {
        $this->builder = $builder;
    }
    
    /**
     * @return bool
     **/
    public function hasParameter(string $name): bool
    {
        return array_key_exists($name, $this->parameters);
    }
    
    /**
     * @function getHttpQuery  proxy method for Client::getHttpQuery()
     *
     * @see Client::getHttpQuery()
     **/
    public function getHttpQuery(): string
    {
        return $this->client->getHttpQuery();
    }
    
    /**
     * @return Client
     **/
    public function getClient(): HttpClient
    {
        return $this->client;
    }

    protected function checkPrerequisites(): bool
    {
        if ( !$this->route ) {
            throw new PrerequisitesException('You need to define a route prior to any request');
        }

        return true;
    }
    
    public function setBasicAuth($user, $password): void
    {
        $auth = base64_encode(sprintf('%s:%s', $user, $password));
        $this->client->addHeader('Authorization', sprintf('%s %s', 'Basic', $auth));
    }
}
