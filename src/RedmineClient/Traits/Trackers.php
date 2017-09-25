<?php

namespace Librinfo\RedmineComponent\RedmineClient\Traits;

trait Trackers
{
    protected function initParameters(): void
    {
        $this->getBuilder()
        ;
    }
    protected function getRoute(): string
    {
        return 'trackers';
    }
}
