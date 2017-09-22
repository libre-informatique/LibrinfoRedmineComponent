<?php

namespace Librinfo\RedmineComponent\Quirks;

use Librinfo\RedmineComponent\Http\Configuration;
use Librinfo\RedmineComponent\Http\Client;
use Librinfo\RedmineComponent\Http\RedmineCookie;

class RedmineAuthenticatedCookieGenerator
{
    /**
     * @var Configuration
     */
    private $configuration;
    
    /**
     * @var Client
     */
    private $client;
    
    /**
     * @var RedmineCookie
     */
    private $cookie;
    
    protected $loginUri = 'login';
    
    public function __construct(Configuration $configuration)
    {
        $this->client = new Client($configuration);
        $this->configuration = $configuration;
    }
    
    public function generateCookie(): void
    {
        $creds = $this->getLoginCredentials();
        $this->cookie = $this->createCookie($this->configuration->getUsername(), $this->configuration->getPassword(), $creds['token'], $creds['cookie']);
    }
    
    public function getCookie(): RedmineCookie
    {
        return $this->cookie;
    }
    
    private function getLoginCredentials(): array
    {
        // the first request
        $r = $this->client->request('GET', $this->configuration->getBaseUrl().$this->loginUri);
        $content = (string)$r->getBody();
        
        // token
        preg_match_all('/<input.+name="authenticity_token".+value="(.+)".*\/>/', $content, $matches);
        $token = $matches[1][0];
        
        // cookie
        $cookie = new RedmineCookie($r->getHeader('Set-Cookie')[0]);
        
        // return values
        return ['token' => $token, 'cookie' => $cookie];
    }
    
    private function createCookie(string $username, string $password, string $token, RedmineCookie $cookie): RedmineCookie
    {
        $r = $this->client->request(
            'POST',
            $this->configuration->getBaseUrl().$this->loginUri,
            [
                'body' => http_build_query([
                    'authenticity_token'    => $token,
                    'username'              => $username,
                    'password'              => $password,
                ]),
                'headers' => [
                    'Content-Type'          => 'application/x-www-form-urlencoded',
                    'Cookie'                => (string)$cookie,
                ],
                'allow_redirects'       => false,
            ]
        );
        
        $cookie = new RedmineCookie($r->getHeader('Set-Cookie')[0]);
        return $cookie;
    }
}
