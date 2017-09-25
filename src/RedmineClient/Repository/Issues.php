<?php

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Traits\Issues as IssuesTrait;
use Librinfo\RedmineComponent\Entity\Issue;

class Issues extends Generic
{
    use IssuesTrait;
    
    protected function getEntityClass(): string
    {
        return Issue::class;
    }
}
