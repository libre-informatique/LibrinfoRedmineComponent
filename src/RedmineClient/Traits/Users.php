<?php

namespace Librinfo\RedmineComponent\RedmineClient\Traits;

trait Users
{
    protected function initParameters(): void
    {
        $this->getBuilder()
            ->addCriterion('group_id')
            ->addCriterion('status')
        ;
    }
    protected function getRoute(): string
    {
        return 'users';
    }
    
    public function setGroup(?int $groupId): void
    {
        $this->getBuilder()
            ->setCurrent('group_id')
            ->setValue($groupId)
        ;
    }
    public function getGroups(): array
    {
        return array_shift($this->getBuilder()
            ->setCurrent('group_id')
            ->getValues())
        ;
    }
    
    public function setStatus(?int $status = NULL): void
    {
        $this->getBuilder()
            ->setCurrent('status')
            ->setValue($status)
        ;
    }
    public function getStatus(): ?int
    {
        return array_shift($this->getBuilder()
            ->setCurrent('status')
            ->getValues())
        ;
    }
}
