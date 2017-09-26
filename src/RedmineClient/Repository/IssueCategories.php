<?php

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Traits\IssueCategories as IssueCategoriesTrait;
use Librinfo\RedmineComponent\Entity\IssueCategory;

class IssueCategories extends Generic
{
    use IssueCategoriesTrait;
    
    protected function getEntityClass(): string
    {
        return IssueCategory::class;
    }
}
