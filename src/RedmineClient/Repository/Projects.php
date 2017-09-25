<?php

namespace Librinfo\RedmineComponent\RedmineClient\Repository;

use Librinfo\RedmineComponent\RedmineClient\Traits\Projects as ProjectsTrait;
use Librinfo\RedmineComponent\Entity\Project;
use Librinfo\RedmineComponent\Core\Collection;

class Projects extends Generic
{
    use ProjectsTrait;
    
    protected function getEntityClass(): string
    {
        return Project::class;
    }
    
    public function getAll(int $max = -1, int $page = 100): Collection
    {
        $projects = parent::getAll($max, $page);
        
        $keys = new Collection(self::class);
        foreach ( $projects as $key => $project ) {
            $keys[$project->get('id')] = $project;
        }
        
        foreach ( $projects as $project ) {
            if ( !$project->has('parent') ) {
                continue;
            }
            
            if ( $project->get('parent')->isFulfilled() ) {
                continue;
            }
            
            $project->set('parent', $keys[$project->get('parent')->get('id')]);
        }
        
        return $projects;
    }
}
