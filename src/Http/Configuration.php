<?php

namespace Librinfo\RedmineComponent\Http;

class Configuration
{
    private $baseUrl;
    private $token;
    private $username;
    private $password;
    
    /**
     * @var Cookie
     **/
    private $cookie;
    
    public function __construct(string $baseUrl, array $auth)
    {
        $this->baseUrl = $baseUrl;
        if ( isset($auth['token']) ) {
            $this->token = $auth['token'];
        }
        if ( isset($auth['username']) && isset($auth['password']) ) {
            $this->username = $auth['username'];
            $this->password = $auth['password'];
        }
        
        if ( isset($auth['cookie']) && $auth['cookie'] instanceof Cookie ) {
            $this->cookie = $auth['cookie'];
        }
    }
    
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
    public function getToken(): string
    {
        return $this->token;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function getCookie(): Cookie
    {
        return $this->cookie;
    }
    public function getBasicAuth(): string
    {
        return 'Basic '.base64_encode(sprintf('%s:%s', $this->username, $this->password));
    }
    public function hasUserAuth(): bool
    {
        return isset($this->username) && isset($this->password);
    }
    public function hasToken(): bool
    {
        return isset($this->token);
    }
    public function hasCookie(): bool
    {
        return isset($this->cookie);
    }
    
    public function setCookie(Cookie $cookie): void
    {
        $this->cookie = $cookie;
    }
}
