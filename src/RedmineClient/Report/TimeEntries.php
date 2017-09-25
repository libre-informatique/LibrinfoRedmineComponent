<?php

namespace Librinfo\RedmineComponent\RedmineClient\Report;

use Librinfo\RedmineComponent\RedmineClient\Traits\TimeEntries as TimeEntriesTrait;

class TimeEntries extends Generic
{
    use TimeEntriesTrait;
    
    protected function defineAvailableCriteria(): array
    {
        return [
            'projects',
            'status',
            'version',
            'category',
            'user',
            'tracker',
            'issue',
        ];
    }
}
