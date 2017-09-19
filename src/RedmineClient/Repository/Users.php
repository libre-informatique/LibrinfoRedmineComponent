<?php

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Traits\Users as UsersTrait;
use Librinfo\RedmineComponent\Entity\User;

class Users extends Generic
{
    use UsersTrait;
    
    protected function getEntityClass(): string
    {
        return User::class;
    }
}
