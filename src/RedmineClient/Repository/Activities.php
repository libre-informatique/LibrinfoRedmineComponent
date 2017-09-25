<?php

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Traits\Activities as ActivitiesTrait;
use Librinfo\RedmineComponent\Entity\Activity;

class Activities extends Generic
{
    use ActivitiesTrait;
    
    protected function getEntityClass(): string
    {
        return Activity::class;
    }
}
