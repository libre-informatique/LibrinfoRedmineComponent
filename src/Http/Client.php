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
use Librinfo\RedmineComponent\IO\IOInterface;
use Librinfo\RedmineComponent\IO\Csv;
use Librinfo\RedmineComponent\IO\Json;
use Librinfo\RedmineComponent\Query\Builder;

class Client extends GuzzleClient
{
    /**
     * @var Configuration
     **/
    private $configuration;

    /**
     * @var string
     **/
    private $route;

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
        $this->configuration = $configuration;

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

    /**
     * @param string $route   can use /project/:project_id format, to replace :project_id by a single value as given to a Builder provided to self::setQuerystring()
     **/
    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    /**
     * Function setQuerystring()
     *
     * If the $qs param is a Builder:
     * tries to replace xyz/:name_id/ by xyz/12 if a value "name_id" is set to 12 (one value strict) in the Builder
     * tries to replace xyz/:name_id/ by "" if 0 or more than 1 "name_id" is set in the Builder
     *
     * @param Builder|string   $qs
     **/
    public function setQuerystring($qs): void
    {
        $this->querystring = (string)$qs;

        if ( ! $qs instanceof Builder ) {
            return;
        }
        $builder = $qs;

        foreach ( $builder->getCriteria() as $criterion ) {
            $values = $builder
                ->setCurrent($criterion)
                ->getValues()
            ;
            $criterion = str_replace('[', '\[', $criterion);
            $criterion = str_replace(']', '\]', $criterion);
            preg_match(sprintf('#(\w+\/(:%s)\/)#', $criterion), $this->route, $matches);

            // if nothing matches
            if ( !$matches ) {
                continue;
            }

            // if values are more than one, or nothing given, clean up the URL
            if ( count($values) != 1 ) {
                $this->route = str_replace($matches[1], '', $this->route);
                continue;
            }

            // if there is a value to substitute
            $this->route = str_replace($matches[2], $values[0], $this->route);
        }
    }
    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getBaseUrl(): string
    {
        return $this->configuration->getBaseUrl();
    }
    public function getRoute(): string
    {
        return $this->route;
    }
    public function getKey(): ?string
    {
        return $this->configuration->getToken();
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
            $this->configuration,
            $this->route,
            $this->querystring,
            $this->format
        ];
    }

    public function getUri(): string
    {
        if ( !$this->route ) {
            throw new RouteException('Please define a route...');
        }

        return $this->configuration->getBaseUrl()
            . $this->route
            . '.'
            . $this->format
            . '?limit=' . $this->limit
            . '&offset=' . $this->offset
            . '&'
            . $this->querystring
        ;
    }

    public function getFullData(int $globalLimit = -1, int $offset = 0, array $options = [], array $results = []): array
    {
        $this->setOffset($offset);

        $data = $this->getData($options)->toArray();
        $results = array_merge($results, $data[$this->getDataKey()]);

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

    public function getData(array $options = []): IOInterface
    {
        // request
        $this->lastResponse = $this->sendRequest($options);

        // result
        $body = $this->lastResponse->getBody();
        return $this->formatData($body);
    }

    public function sendRequest(array $options = [])
    {
        // headers
        if ( !isset($option['headers']) ) {
            $options['headers'] = [];
        }

        if ( $this->configuration->hasToken() ) {
            $options['headers']['X-Redmine-API-Key'] = $this->configuration->getToken();
        }
        if ( $this->configuration->hasUserAuth() ) {
            $options['headers']['Authorization'] = $this->configuration->getBasicAuth();
        }
        $options['headers'] = array_merge($options['headers'], $this->headers);

        // request
        return parent::request($this->method, $this->getUri(), $options);

    }

    protected function formatData(string $body): IOInterface
    {
        switch ( $this->format ) {
            case 'json':
                return new Json($body, ['assoc' => true]);
            case 'csv':
                return new Csv($body, ['delimiter' => ';', 'encodings' => ['LATIN1', 'UTF8']]);
            default:
                return new Plain($body);
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
     * @function addCookie    add a cookie header for the request
     *
     * @param Cookie $cookie
     **/
    public function addCookie(Cookie $cookie): void
    {
        $this->addHeader('Cookie', $cookie);
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

    private function getDataKey(): string
    {
        return preg_replace('!^(.*/)!', '', $this->route);
    }
}
