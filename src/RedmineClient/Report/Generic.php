<?php

namespace Librinfo\RedmineComponent\RedmineClient\Report;

use Librinfo\RedmineComponent\RedmineClient\Client as RedmineClient;
use Librinfo\RedmineComponent\Http\Configuration;
use Librinfo\RedmineComponent\Http\RedmineCookie;
use Librinfo\RedmineComponent\Exception\PrerequisitesException;
use Librinfo\RedmineComponent\Quirks\RedmineAuthenticatedCookieGenerator;
use Librinfo\RedmineComponent\IO\IOInterface;

abstract class Generic extends RedmineClient
{
    /**
     * @var RedmineCookie
     */
    private $cookie;
    
    /**
     * @var string
     */
    private $columns = 'month';
    
    /**
     * @var string
     */
    private $criterias = [];
    
    /**
     * @var array
     **/
    private $availableCriteria = [];
    
    abstract protected function defineAvailableCriteria(): array;
    
    public function __construct(Configuration $configuration)
    {
        parent::__construct($configuration);
        $this->getClient()->setFormat('csv');
        $this->getClient()->setRoute($this->getClient()->getRoute().'/report');
        
        if ( !$configuration->hasCookie() ) {
            $cookiegen = new RedmineAuthenticatedCookieGenerator($configuration);
            $cookiegen->generateCookie();
            $this->cookie = $cookiegen->getCookie();
        }
        
        $this->defineAvailableCriteria();
        $this->clearCriteria();
    }
    
    public function getAvailableCriteria(): array
    {
        return $this->availableCriteria;
    }
    
    public function getCookieGenerator(): RedmineAuthenticatedCookieGenerator
    {
        return $this->cookiegen;
    }
    
    public function clearCriteria(): void
    {
        $this->criterias = [];
    }
    public function addCriterion(string $criteria): void
    {
        $this->criterias[] = $criteria;
    }
    public function getCriteria(): array
    {
        return $this->criterias;
    }
    
    public function setColumns(string $columns): void
    {
        $this->columns = $columns;
    }
    
    /**
     * @function get()  get formatted data
     *
     * @return array    representing the report
     */
    public function get(): IOInterface
    {
        $criterias = 'criteria[]='.implode('&criteria[]=', $this->criterias);
        
        $this->getClient()->addCookie($this->cookie);
        $this->getClient()->setQuerystring($this->getBuilder().'&columns='.$this->columns.'&'.$criterias);
        
        return $this->getClient()->getData();
    }
}
