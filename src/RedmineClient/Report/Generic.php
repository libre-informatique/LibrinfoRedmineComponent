<?php

namespace Librinfo\RedmineComponent\RedmineClient\Report;

use Librinfo\RedmineComponent\RedmineClient\Client as RedmineClient;
use Librinfo\RedmineComponent\Http\Configuration;
use Librinfo\RedmineComponent\Http\RedmineCookie;
use Librinfo\RedmineComponent\Exception\PrerequisitesException;

abstract class Generic extends RedmineClient
{
    /**
     * @var RedmineCookie
     */
    private $cookie;
    
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);
        $this->getClient()->setFormat('csv');
        $this->getClient()->setRoute($this->getClient()->getRoute().'/report');
    }
    
    public function setCookie(RedmineCookie $cookie): void
    {
        $this->cookie = $cookie;
        $this->getClient()->addHeader('Cookie', $this->cookie);
    }
    public function getCookie(RedmineCookie $cookie): RedmineCookie
    {
        return $this->cookie;
    }
    
    /**
     * @function get()  get formatted data
     *
     * @return array    representing the report
     */
    public function get(): array
    {
        $this->checkPrerequisites();
        $this->getClient()->setQuerystring($this->getBuilder().'&columns=month&criteria[]=project');
        
        return $this->getClient()->getData();
    }
    
    protected function checkPrerequisites(): bool
    {
        parent::checkPrerequisites();
        
        if ( !$this->cookie ) {
            throw new PrerequisitesException(sprintf('[%s] You need to provide a Cookie before any request', self::class));
        }
        
        return true;
    }
}
