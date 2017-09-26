<?php

namespace Librinfo\RedmineComponent\Entity;

use Librinfo\RedmineComponent\Entity\Project;
use Librinfo\RedmineComponent\Entity\User;

class IssueCategory extends Entity
{
    protected $id;
    protected $name;
    protected $project;
    protected $assigned_to;

    protected function hydrateValue($value, ?string $name = NULL)
    {
        if ( !$this->isElligibleToSpecialHydration($value, $name) ) {
            return $value;
        }
        
        switch ( $name ) {
            case 'project':
                return Project::create($value);
            case 'assignedTo':
                return User::create($value);
        }
        
        return parent::hydrateValue($value, $name);
    }
}
