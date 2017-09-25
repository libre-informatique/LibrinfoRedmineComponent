<?php

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Traits\IssueStatuses as IssueStatusesTrait;
use Librinfo\RedmineComponent\Entity\IssueStatus;

class IssueStatuses extends Generic
{
    use IssueStatusesTrait;
    
    protected function getEntityClass(): string
    {
        return IssueStatus::class;
    }
}
