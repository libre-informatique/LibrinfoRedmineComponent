<?php

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Traits\Trackers as TrackersTrait;
use Librinfo\RedmineComponent\Entity\Tracker;

class Trackers extends Generic
{
    use TrackersTrait;
    
    protected function getEntityClass(): string
    {
        return Tracker::class;
    }
}
