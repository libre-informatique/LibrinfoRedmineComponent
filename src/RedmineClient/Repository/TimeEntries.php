<?php

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Traits\TimeEntries as TimeEntriesTrait;
use Librinfo\RedmineComponent\Entity\TimeEntry;

class TimeEntries extends Generic
{
    use TimeEntriesTrait;
    
    protected function getEntityClass(): string
    {
        return TimeEntry::class;
    }
}
