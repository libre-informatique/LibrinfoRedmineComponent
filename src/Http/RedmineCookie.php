<?php

namespace Librinfo\RedmineComponent\Http;

use Librinfo\RedmineComponent\Utils\StringConverter;
use Librinfo\RedmineComponent\Exception\PrerequisitesException;

class RedmineCookie extends Cookie
{
    public function __construct(string $name, ?string $value = NULL)
    {
        $sc = new StringConverter;
        $tmp = [
            'name'  => $sc->extractCookieName($name),
            'value' => $value ? $value : $sc->extractCookieKey($name),
        ];
        
        parent::__construct($tmp['name'], $tmp['value']);
        
        if ( !$this->isRedmineCompatible() ) {
            throw new PrerequisitesException(sprintf('[%s] The given cookie does not fit Redmine needs.', self::class));
        }
    }
    
    protected function isRedmineCompatible(): bool
    {
        return strpos($this->getName(), 'redmine') !== false;
    }
}
