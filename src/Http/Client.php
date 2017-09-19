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

namespace Librinfo\RedmineComponent\Http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Response;
use Librinfo\RedmineComponent\Exception\RouteException;
use Librinfo\RedmineComponent\Http\Cookie;

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
     * @var array
     **/
    private $headers = [];
    
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
        $this->setKey($configuration->getKey());
        
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
    public function setOffset(?int $offset = 0): void
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
        $this->addHeader('X-Redmine-API-Key', $key);
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
            throw new RouteException('Please define a route...');
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
    
    public function getFullData(int $globalLimit = -1, int $offset = 0, array $options = [], array $results = []): array
    {
        $this->setOffset($offset);
        
        $data = $this->getData($options);
        $results = array_merge($results, $data[$this->getRoute()]);
        
        if ( !$this->isDataPaginated($data) ) {
            return $results;
        }
        
        if ( $globalLimit >= 0 && count($results) >= $globalLimit ) {
            return $results;
        }
        
        if ( $data['total_count'] - 1 < ($data['offset'] + 1) * $data['limit'] ) {
            return $results;
        }
        
        return $this->getFullData($globalLimit, $offset+1, $options, $results);
    }
    
    protected function isDataPaginated(array $data): bool
    {
        foreach ( ['total_count', 'offset', 'limit'] as $key ) {
            if ( !isset($data[$key]) ) {
                return false;
            }
        }
        return true;
    }
    
    public function getHttpQuery(): string
    {
        return sprintf('%s %s', $this->method, $this->getUri());
    }
    
    public function getData(array $options = []): array
    {
        // headers
        if ( !isset($option['headers']) ) {
            $options['headers'] = [];
        }
        $options['headers'] = array_merge($options['headers'], $this->headers);
        
        // request
        $this->lastResponse = parent::request($this->method, $this->getUri(), $options);
        
        // result
        $body = $this->lastResponse->getBody();
        $r = $this->formatData($body);
        
        return $r;
    }
    
    protected function formatData(string $body): array
    {
        switch ( $this->format ) {
            case 'json':
                return json_decode($body, true);
            case 'csv':
                $r = [];
                foreach ( explode("\n", $body) as $line ) {
                    $r[] = str_getcsv(iconv('LATIN1', 'UTF8', $line), ';');
                }
                return $r;
            default:
                return [(string)$body];
        }
    }
    
    /**
     * @function setHeaders   set all headers for the request
     *
     * @param array $headers  array of keys => values headers
     **/
    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }
    /**
     * @function addHeader    add one header for the request
     *
     * @param string $name    header name
     * @param string $value   header value
     **/
    public function addHeader(string $name, string $value): void
    {
        $this->headers[$name] = $value;
    }
    
    /**
     * @function getHeaders get all headers of the request
     *
     * @return array
     **/
    public function getRequestHeaders(): array
    {
        return $this->headers;
    }
    
    /**
     * @function getHeaders get all headers of the response
     *
     * @return array
     **/
    public function getHeaders(): array
    {
        return $this->lastResponse->getHeaders();
    }
    /**
     * @function addHeader  get one header of the response
     *
     * @return array|string|null
     **/
    public function getHeader(string $header)
    {
        return $this->lastResponse->getHeader($header);
    }
    
    public function getLastResponse(): Response
    {
        return $this->lastResponse;
    }
}
