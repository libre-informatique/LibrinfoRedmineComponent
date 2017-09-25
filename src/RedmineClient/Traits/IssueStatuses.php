<?php

namespace Librinfo\RedmineComponent\RedmineClient\Traits;

trait IssueStatuses
{
    protected function initParameters(): void
    {
        $this->getBuilder()
        ;
    }
    protected function getRoute(): string
    {
        return 'issue_statuses';
    }
}
