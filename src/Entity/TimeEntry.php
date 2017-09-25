<?php

namespace Librinfo\RedmineComponent\Entity;

class TimeEntry extends Entity
{
    protected $id;
    protected $project;
    protected $issue;
    protected $user;
    protected $activity;
    protected $hours;
    protected $comments;
    protected $spentOn;
    protected $createdOn;
    protected $updatedOn;
    
    public function __toString(): string
    {
        return $this->hours.' ('.$this->activity['name'].' | '.$this->project['name'].')';
    }
    
    protected function hydrateValue($value, ?string $name = NULL)
    {
        if ( !$this->isElligibleToSpecialHydration($value, $name) ) {
            return $value;
        }
        
        switch ( $name ) {
            case 'project':
                return Project::create($value);
            case 'issue':
                return Issue::create($value);
            case 'user':
                return User::create($value);
            case 'activity':
                return Activity::create($value);
        }
        
        return $value;
    }
}
