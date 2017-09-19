<?php

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Traits\Groups as GroupsTrait;
use Librinfo\RedmineComponent\Entity\Group;

class Groups extends Generic
{
    use GroupsTrait;
    
    protected function getEntityClass(): string
    {
        return Group::class;
    }
}
