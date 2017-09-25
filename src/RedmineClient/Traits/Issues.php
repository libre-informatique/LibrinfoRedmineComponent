<?php

namespace Librinfo\RedmineComponent\RedmineClient\Traits;

trait Issues
{
    protected function initParameters(): void
    {
        $this->getBuilder()
            ->addCriterion('status_id')
            ->addCriterion('tracker_id')
            ->addCriterion('author_id')
            ->addCriterion('assigned_to_id')
            ->addCriterion('member_of_group')
            ->addCriterion('assigned_to_role')
            ->addCriterion('subject')
            ->addCriterion('created_on')
            ->addCriterion('updated_on')
            ->addCriterion('start_date')
            ->addCriterion('due_date')
            ->addCriterion('estimated_hours')
            ->addCriterion('done_ratio')
            ->addCriterion('is_private')
            ->addCriterion('watcher_id')
            ->addCriterion('relates')
            ->addCriterion('duplicates')
            ->addCriterion('duplicated')
            ->addCriterion('blocks')
            ->addCriterion('blocked')
            ->addCriterion('precedes')
            ->addCriterion('follows')
            ->addCriterion('copies_to')
            ->addCriterion('copied_from')
        ;
    }
    protected function getRoute(): string
    {
        return 'issues';
    }
}
