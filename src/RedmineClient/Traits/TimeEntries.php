<?php

namespace Librinfo\RedmineComponent\RedmineClient\Traits;

use Librinfo\RedmineComponent\Query\Operand;

trait TimeEntries
{
    protected function initParameters(): void
    {
        $this->getBuilder()
            ->addCriterion('user_id')
        ;
    }
    protected function getRoute(): string
    {
        return 'time_entries';
    }
    
    public function addUser(?int $userId, ?string $operand = 'equal'): void
    {
        $this->getBuilder()
            ->setCurrent('user_id')
            ->addValue($userId)
            ->setOperand(new Operand($operand));
        ;
    }
    public function setUsers(array $users, ?string $operand = 'equal'): void
    {
        $this->getBuilder()
            ->setCurrent('user_id')
            ->setValues($users)
            ->setOperand(new Operand($operand));
        ;
    }
    public function getUsers(): array
    {
        return $this->getBuilder()
            ->setCurrent('user_id')
            ->getValues()
        ;
    }
}
