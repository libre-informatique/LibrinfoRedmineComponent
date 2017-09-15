<?php

use \Librinfo\RedmineComponent\Client;
use \Librinfo\RedmineComponent\Configuration;
use \Librinfo\RedmineComponent\Repository\Users;

class Query
{
    /**
     * @var Client
     **/
    private $client;
    
    /**
     * @var Configuration
     **/
    
    private $configuration;
    
    public function exec()
    {
        $this->configuration = $this->createConfiguration();
        $this->client = $this->createClient();
        
        if ( isset($_SERVER['argv'][3]) ) {
            print_r($this->execCommandLineUri());
        }
        
        $users = new Users($this->configuration);
        $users->unsetStatus();
        $users->setGroup(8);
        foreach ( $users->getAll() as $user ) {
            echo $user;
            print_r($user->toArray());
        }
    }
    
    private function getValidUsers(array $users)
    {
        $validUsers = [];
        foreach ( $users as $user ) {
            if ( preg_match('/@libre-informatique\.fr$/i', $user['mail']) === 1 ) {
                $validUsers[] = $user;
            }
        }
        return $validUsers;
    }
    
    protected function execRequest(string $type = 'GET'): array
    {
        $this->client->setMethod($type);
        $res = $this->client->getData();
        return $res->getBody();
    }
    
    private function execCommandLineUri(): array
    {
        $this->client->setRoute($_SERVER['argv'][3]);
        if ( isset($_SERVER['argv'][4]) ) {
            $this->client->setQuerystring($_SERVER['argv'][4]);
        }
        if ( isset($_SERVER['argv'][5]) ) {
            $this->client->setFormat($_SERVER['argv'][5]);
        }
        
        return $this->client->getData();
    }
    
    private static function createConfiguration(): Configuration
    {
        $i = 1;
        $configuration = new Configuration(
            $_SERVER['argv'][$i++], // BaseURL
            $_SERVER['argv'][$i++]  // Key
        );
        
        return $configuration;
    }
    private function createClient(): Client
    {
        return new Client($this->configuration);
    }
}
