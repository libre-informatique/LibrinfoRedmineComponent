<?php

use Librinfo\RedmineComponent\Http\Client;
use Librinfo\RedmineComponent\Http\Configuration;
use Librinfo\RedmineComponent\Http\RedmineCookie;
use Librinfo\RedmineComponent\Core\Collection;
use Librinfo\RedmineComponent\RedmineClient\Repository\Trackers;
use Librinfo\RedmineComponent\RedmineClient\Repository\IssueStatuses;
use Librinfo\RedmineComponent\RedmineClient\Repository\Issues;
use Librinfo\RedmineComponent\RedmineClient\Repository\Projects;
use Librinfo\RedmineComponent\RedmineClient\Repository\Users;
use Librinfo\RedmineComponent\RedmineClient\Repository\Groups;
use Librinfo\RedmineComponent\RedmineClient\Repository\TimeEntries;
use Librinfo\RedmineComponent\RedmineClient\Repository\IssueCategories;
use Librinfo\RedmineComponent\RedmineClient\Report\TimeEntries as ReportTimeEntries;
use Librinfo\RedmineComponent\Utils\StringConverter;
use Librinfo\RedmineComponent\Core\Context;

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
        
        $i = $_SERVER['argv'][2] == 'username' ? 5 : 3;
        if ( isset($_SERVER['argv'][$i]) ) {
            $this->print($this->execCommandLineUri());
        }
        
        // issue categories
        $this->processIssueCategories();
        
        $trackers = $this->processTrackers();
        $issues = $this->processIssues();
        
        $issueStatuses = $this->processIssueStatuses();
        $projects = $this->processProjects();
        
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
        $auth = $_SERVER['argv'][2] == 'username'
            ? ['username' => $_SERVER['argv'][3], 'password' => $_SERVER['argv'][4]] // username + password
            : ['token' => $_SERVER['argv'][2]] // Token
        ;
        
        $configuration = new Configuration(
            $_SERVER['argv'][1], // BaseURL
            $auth
        );
        
        return $configuration;
    }
    private function createClient(): Client
    {
        return new Client($this->configuration);
    }
    
    private function processIssueCategories(): Collection
    {
        $repo = new IssueCategories($this->configuration);
        $repo->getBuilder()
            ->setCurrent('project_id')
            ->addValue('libre-informatique')
        ;
        
        $ics = $repo->getAll();
        $this->print($ics);
        $this->print($repo->getHttpQuery());
        
        return $ics;
    }
    private function processTrackers(): Collection
    {
        $repo = new Trackers($this->configuration);
        
        $trackers = $repo->getAll();
        $this->print($trackers);
        $this->print($repo->getHttpQuery());
        
        return $trackers;
    }
    private function processIssueStatuses(): Collection
    {
        $repo = new IssueStatuses($this->configuration);
        
        //$this->print($repo->getOne(4534));
        $statuses = $repo->getAll();
        $this->print($statuses);
        $this->print($repo->getHttpQuery());
        
        return $statuses;
    }
    private function processIssues(): Collection
    {
        $repo = new Issues($this->configuration);
        
        $issues = $repo->getAll(100);
        $this->print($issues);
        $this->print($repo->getHttpQuery());
        $this->print($repo->getOne($issues->getRandom()->get('id')));
        
        return $issues;
    }
    private function processProjects(): Collection
    {
        $repo = new Projects($this->configuration);
        $repo->setClosed(true);
        
        $projects = $repo->getAll();
        $this->print($projects);
        $this->print($repo->getHttpQuery());
        
        $project = $repo->getOne($projects->getRandom()->get('id'));
        $this->print($project);
        
        return $projects;
    }
    
    private function processTimeEntries(array $users): Collection
    {
        $sc = new StringConverter;
        $repo = new TimeEntries($this->configuration);
        
        $repo->setUsers($users);
        $tes = $repo->getAll(50);
        $this->print($tes);
        $this->print($repo->getHttpQuery());
        
        $this->print($repo->getOne($tes->getRandom()->get('id')));
        
        $repo = new ReportTimeEntries($this->configuration);
        $repo->setUsers($users);
        $repo->addCriterion('project');
        
        $csv = $repo->get();
        //$this->print($csv->toArray());
        
        return $tes;
    }
    
    private function processUsers(?int $group = NULL): Collection
    {
        $repo = new Users($this->configuration);
        $repo->setStatus();
        $repo->setGroup($group);
        $users = $repo->getAll();
        $this->print($users);
        return $users;
    }
    
    private function processUser(int $userId)
    {
        $repo = new Users($this->configuration);
        $repo->setStatus();
        $user = $repo->getOne($userId);
        $this->print($user);
        return $user;
    }
    
    private function processGroups(): Collection
    {
        $repo = new Groups($this->configuration);
        $groups = $repo->getAll();
        $this->print($groups);
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
    
    protected function execRequest(string $type = 'GET', ?array $options = []): array
    {
        $this->client->setMethod($type);
        $res = $this->client->getData($options);
        return $res->getBody();
    }
    
    protected function print($data, ?string $type = 'INFO'): void
    {
        dump(sprintf('[%s]', $type), $data);
    }
    
    private function execCommandLineUri(): array
    {
        $i = $_SERVER['argv'][2] == 'username' ? 5 : 3;
        $this->client->setRoute($_SERVER['argv'][$i]);
        if ( isset($_SERVER['argv'][$i+1]) ) {
            $this->client->setQuerystring($_SERVER['argv'][$i+1]);
        }
        if ( isset($_SERVER['argv'][$i+2]) ) {
            $this->client->setFormat($_SERVER['argv'][$i+2]);
        }
        
        return $this->client->getData();
    }
}
