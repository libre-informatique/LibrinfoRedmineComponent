<?php

namespace Librinfo\RedmineComponent\RedmineClient\Traits;

trait IssueCategories
{
    protected function initParameters(): void
    {
        $this->getBuilder()
            ->addCriterion('project_id')
        ;
    }
    protected function getRoute(): string
    {
        return 'projects/:project_id/issue_categories';
    }
}
