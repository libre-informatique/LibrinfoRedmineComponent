<?php

namespace Librinfo\RedmineComponent\RedmineClient\Traits;

trait Projects
{
    protected function initParameters(): void
    {
        $this->getBuilder()
            ->addCriterion('closed')
        ;
    }
    protected function getRoute(): string
    {
        return 'projects';
    }
    
    public function setClosed(bool $bool): void
    {
        // useless, unimplemented using the API
        $this->getBuilder()
            ->setCurrent('closed')
            ->setValue($bool)
        ;
    }
    public function getClosed(): bool
    {
        return array_shift($this->getBuilder()
            ->setCurrent('closed')
            ->getValue())
        ;
    }
}
