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
}
