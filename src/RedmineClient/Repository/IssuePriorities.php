<?php

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Traits\IssuePriorities as IssuePrioritiesTrait;
use Librinfo\RedmineComponent\Entity\IssuePriority;

class IssuePriorities extends Generic
{
    use IssuePrioritiesTrait;
    
    protected function getEntityClass(): string
    {
        return IssuePriority::class;
    }
}
