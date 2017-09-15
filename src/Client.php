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

namespace Librinfo\RedmineComponent;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;

class Client extends GuzzleClient
{
    /**
     * @var string
     **/
    private $baseUrl;
    
    /**
     * @var string
     **/
    private $route;
    
    /**
     * @var string
     **/
    private $key;
    
    /**
     * @var string
     **/
    private $querystring = '';
    
    /**
     * @var string
     **/
    private $format = 'json';
    
    /**
     * @var Response
     */
    private $lastResponse;
    
    /**
     * @var int
     **/
    private $offset = 0;
    
    /**
     * @var int
     **/
    private $limit = 25;
    
    /**
     * @var string
     **/
    private $method = 'GET';
    
    public function __construct(Configuration $configuration)
    {
        $this->baseUrl = $configuration->getBaseUrl();
        $this->key = $configuration->getKey();
        
        parent::__construct();
    }
    
    public function setLimit(int $limit = 25): void
    {
        $this->limit = $limit;
    }
    public function getLimit(): int
    {
        return $this->limit;
    }
    public function setMethod(string $method = 'GET'): void
    {
        $this->method = $method;
    }
    public function getMethod(): string
    {
        return $this->method;
    }
    public function setOffset(int $offset = 0): void
    {
        $this->offset = $offset;
    }
    public function getOffset(): int
    {
        return $this->offset;
    }
    public function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }
    public function setRoute(string $route): void
    {
        $this->route = $route;
    }
    public function setKey(string $key): void
    {
        $this->key = $key;
    }
    public function setQuerystring(string $qs = ''): void
    {
        $this->querystring = $qs;
    }
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }
    
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
    public function getRoute(): string
    {
        return $this->route;
    }
    public function getKey(): string
    {
        return $this->key;
    }
    public function getQuerystring(): string
    {
        return $this->querystring;
    }
    public function getFormat(): string
    {
        return $this->format;
    }
    
    public function getParameters(): array
    {
        return [
            $this->baseUrl,
            $this->route,
            $this->key,
            $this->querystring,
            $this->format
        ];
    }
    
    public function getUri(): string
    {
        if ( !$this->route ) {
            throw new Exception\RouteException('Please define a route...');
        }
        
        return $this->baseUrl
            . $this->route
            . '.'
            . $this->format
            . '?key=' . $this->key
            . '&limit=' . $this->limit
            . '&offset=' . $this->offset
            . '&'
            . $this->querystring
        ;
    }
    
    public function getFullData($offset = 0, array $options = [], array $results = []): array
    {
        $this->setOffset($offset);
        
        $data = $this->getData($options);
        $results = array_merge($results, $data[$this->getRoute()]);
        
        if ( $data['total_count'] - 1 < ($data['offset'] + 1) * $data['limit'] ) {
            return $results;
        }
        
        return $this->getFullData($offset+1, $options, $results);
    }
    
    public function getData(array $options = []): array
    {
        $this->lastResponse = parent::request($this->method, $this->getUri(), $options);
        return json_decode($this->lastResponse->getBody(), true);
    }
    
    public function getLastResponse(): Response
    {
        return $this->lastResponse;
    }
}
