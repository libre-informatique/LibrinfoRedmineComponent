<?php

namespace Librinfo\RedmineComponent\Entity;

class Issue extends Entity
{
    protected $id;
    protected $project;
    protected $tracker;
    protected $status;
    protected $priority;
    protected $author;
    protected $subject;
    protected $description;
    protected $doneRatio;
    protected $customFields;
    protected $createdOn;
    protected $updatedOn;
    protected $assignedTo;
    protected $startDate;
    protected $dueDate;
    protected $estimatedHours;
    protected $parent;
    
    public function isFulfilled(): bool
    {
        return parent::isFulfilled() && $this->has('project');
    }
    
    protected function hydrateValue($value, ?string $name = NULL)
    {
        if ( !$this->isElligibleToSpecialHydration($value, $name) ) {
            return $value;
        }
        
        switch ( $name ) {
            case 'parent':
                return self::create($value);
            case 'assignedTo':
            case 'author':
                return User::create($value);
            case 'project':
                return Project::create($value);
            case 'status':
                return IssueStatus::create($value);
            case 'tracker':
                return Tracker::create($value);
            case 'priority':
                return IssuePriority::create($value);
        }
        
        return parent::hydrateValue($value, $name);
    }
}
