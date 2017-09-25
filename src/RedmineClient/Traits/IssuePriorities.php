<?php

namespace Librinfo\RedmineComponent\RedmineClient\Traits;

trait IssuePriorities
{
    protected function initParameters(): void
    {
        $this->getBuilder()
        ;
    }
    protected function getRoute(): string
    {
        return 'enumerations/issue_priorities';
    }
}
