<?php

namespace Librinfo\RedmineComponent\Repository;

use Librinfo\RedmineComponent\Client;
use Librinfo\RedmineComponent\Configuration;

class Users
{
    /**
     * @var Client
     */
    private $client;
    
    /**
     * @var string
     */
    private $route = 'users';
    
    public function __construct(Configuration $configuration)
    {
        $this->client = new Client($configuration);
    }
    
    public function getQuerystring(): string
    {
        $options = $this->getOptions();
        $qs = [];
        foreach ( $options as $name => $value ) {
            $qs[] = $name . '=' . $value;
        }
        return implode('&', $qs);
    }
    
    public function getAll(): array
    {
        $this->client->setMethod();
        $this->client->setRoute($this->route);
        $this->client->setQuerystring($this->getQuerystring());
        return $this->client->getFullData();
    }
    
    public function getOne(int $id): array
    {
        $this->client->setMethod();
        $this->client->setRoute($this->route.'/'.$id);
        return $this->client->getFullData();
    }
    
    public function unsetGroup(): void
    {
        $this->removeOption('group_id');
    }
    public function setGroup(int $groupId): void
    {
        $this->setOption('group_id', $groupId);
    }
    public function unsetStatus(): void
    {
        $this->setOption('status', '');
    }
    public function setStatus(int $status): void
    {
        $this->setOption('status', $status);
    }
    
    protected function getOptions(): array
    {
        return $this->options;
    }
    protected function getOption(string $name)
    {
        return $this->options[$name];
    }
    protected function removeOption(string $name): void
    {
        unset($this->options[$name]);
    }
    protected function setOption(string $name, $value): void
    {
        $this->options[$name] = $value;
    }
}
