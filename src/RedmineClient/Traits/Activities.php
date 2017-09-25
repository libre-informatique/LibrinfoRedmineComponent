<?php

namespace Librinfo\RedmineComponent\RedmineClient\Traits;

trait Activities
{
    protected function initParameters(): void
    {
        $this->getBuilder()
        ;
    }
    protected function getRoute(): string
    {
        return 'enumerations/time_entry_activities';
    }
}
