<?php

namespace Librinfo\RedmineComponent\Core;

class Context
{
    /**
     * @var array  of Collection
     **/
    private $catalog;
    
    public function __construct()
    {
        $this->catalog = new Catalog;
    }
    
    public function getCatalog(): Catalog
    {
        return $this->catalog;
    }
    
    public static function getInstance(): Context
    {
        global $__librinfo_redminecomponent_context;
        
        if ( !self::hasInstance() ) {
            self::initInstance();
        }
        
        return $__librinfo_redminecomponent_context;
    }
    
    public static function hasInstance(): bool
    {
        global $__librinfo_redminecomponent_context;
        return isset($__librinfo_redminecomponent_context);
    }
    
    public static function initInstance(): void
    {
        global $__librinfo_redminecomponent_context;
        $__librinfo_redminecomponent_context = new self;
    }
}
