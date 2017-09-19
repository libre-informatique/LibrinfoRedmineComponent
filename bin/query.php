<?php

use Librinfo\RedmineComponent\Http\Client;
use Librinfo\RedmineComponent\Http\Configuration;
use Librinfo\RedmineComponent\Http\RedmineCookie;
use Librinfo\RedmineComponent\Collection\Collection;
use Librinfo\RedmineComponent\RedmineClient\Repository\Users;
use Librinfo\RedmineComponent\RedmineClient\Repository\Groups;
use Librinfo\RedmineComponent\RedmineClient\Repository\TimeEntries;
use Librinfo\RedmineComponent\RedmineClient\Report\TimeEntries as ReportTimeEntries;
use Librinfo\RedmineComponent\Utils\StringConverter;

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
        
        // groups
        $groups = $this->processGroups();
        $group  = $this->findLIGroup($groups);
        if ( !$group ) {
            error_log('ERROR FIND GROUP');
            return;
        }
        
        // users
        $users = $this->processUsers($group->get('id'));
        $user1  = $this->processUser($users->getRandom()->get('id'));
        $user2  = $this->processUser($users->getRandom()->get('id'));
        
        // time entries
        $this->processTimeEntries([
            $user1->get('id'),
            $user2->get('id'),
        ]);
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
    
    private function processTimeEntries(array $users): Collection
    {
        $sc = new StringConverter;
        $repo = new TimeEntries($this->configuration);
        
        $repo->setUsers($users);
        $tes = $repo->getAll(50);
        print_r($tes->toArray(true));
        
        $cookies = [];
        foreach ( $repo->getClient()->getHeader('Set-Cookie') as $cookie ) {
            try {
                $cookies[] = new RedmineCookie($cookie);
            }
            catch ( PrerequisitesException $e ) {
            }
        }
        
        $this->print($repo->getHttpQuery());
        
        $repo = new ReportTimeEntries($this->configuration);
        $repo->setUsers($users);
        //$repo->setCookie(new \Librinfo\RedmineComponent\Http\RedmineCookie('_redmine_default=BAh7D0kiD3Nlc3Npb25faWQGOgZFVEkiJWQ1MGVkMTE0ZTk2MTBkYWM2ZjZlZGU3M2UwODBhMzZjBjsAVEkiDHVzZXJfaWQGOwBGaQlJIgpjdGltZQY7AEZsKwcXZbFZSSIKYXRpbWUGOwBGbCsHe1DBWUkiF3RpbWVsb2dfaW5kZXhfc29ydAY7AEZJIhJzcGVudF9vbjpkZXNjBjsAVEkiEF9jc3JmX3Rva2VuBjsARkkiMVFOTzZlVlBZdkJBc0ROd3NmOFdPbXBINXRUUDl4YThlbzFUeFB2VHB5QzA9BjsARkkiCnF1ZXJ5BjsARnsHOgdpZGkdOg9wcm9qZWN0X2lkMEkiFmlzc3Vlc19pbmRleF9zb3J0BjsARkkiDGlkOmRlc2MGOwBUSSIVdXNlcnNfaW5kZXhfc29ydAY7AEZJIgpsb2dpbgY7AFRJIg1wZXJfcGFnZQY7AEZpaQ%3D%3D--749fa062b196e03bdf8f2cdf0c5fd8215263b67d; path=/; HttpOnly'));
        foreach ( $cookies as $cookie ) {
            $repo->setCookie($cookie);
        }
        /*
        $this->print('TEST');
        
        $r = $this->client->request('GET', 'https://suivi.libre-informatique.fr/time_entries/report.csv?key=620f0814e061fdfb17ffe74fc4bf1130569ce7cd&f[]=user_id&v[user_id][]=5&v[user_id][]=4&op[user_id]=%3D&columns=month&criteria[]=project', [
            'Cookie' => (string)$cookie,
        ]);
        $this->print('TEST2');
        echo $r->getBody();
        */
        
        $this->print('TEST3');
        print_r($repo->getClient()->getRequestHeaders());
        $this->print($repo->getHttpQuery());
        print_r($repo->get());
        
        return $tes;
    }
    
    private function processUsers(?int $group = NULL): Collection
    {
        $repo = new Users($this->configuration);
        $repo->setStatus();
        $repo->setGroup($group);
        $users = $repo->getAll();
        print_r($users->toArray(true));
        return $users;
    }
    
    private function processUser(int $userId)
    {
        $repo = new Users($this->configuration);
        $repo->setStatus();
        $user = $repo->getOne($userId);
        print_r($user->toArray(true));
        return $user;
    }
    
    private function processGroups(): Collection
    {
        $repo = new Groups($this->configuration);
        $groups = $repo->getAll();
        print_r($groups->toArray(true));
        return $groups;
    }
    
    private function findLIGroup($groups)
    {
        foreach ( $groups as $group ) {
            if ( $group->get('name') == "00 Toute l'équipe LI" ) {
                return $group;
            }
        }
        return NULL;
    }
    
    protected function execRequest(string $type = 'GET'): array
    {
        $this->client->setMethod($type);
        $res = $this->client->getData();
        return $res->getBody();
    }
    
    protected function print(string $str, ?string $type = 'INFO'): void
    {
        echo sprintf("[%s] %s\n", $type, $str);
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
}
