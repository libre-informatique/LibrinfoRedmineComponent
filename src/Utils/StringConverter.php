<?php

namespace Librinfo\RedmineComponent\Utils;

class StringConverter
{
    public function fromSnakeCaseToCamelCase(string $string): string
    {
        return preg_replace_callback(
            '/(_\w)/',
            function($m){ return strtoupper($m[0][1]); },
            $string
        );
    }
    public function fromCamelCaseToSnakeCase(string $string): string
    {
        return preg_replace_callback(
            '/(.?[A-Z])+/',
            function($m){ return strtolower(strlen($m[0]) == 1 ? $m[0] : $m[0][0].'_'.$m[0][1]); },
            $string
        );
    }
    
    public function removeArrayIndexesFromQuerystring(string $qs): string
    {
        return preg_replace('/%5B\d+%5D/', '%5B%5D', $qs);
    }
    
    public function UrlDecode(string $url): string
    {
        return urldecode($url);
    }
    public function UrlEncode(string $url): string
    {
        return urlencode($url);
    }
    
    public function extractCookieName(string $header): ?string
    {
        preg_match('/^(.+?)=/', $header, $matches);
        
        if ( !isset($matches[1]) ) {
            return NULL;
        }
        return $matches[1];
    }
    
    public function extractCookieKey(string $header): ?string
    {
        preg_match('/^.+?=([\d\w\-%]+);/', $header, $matches);
        
        if ( !isset($matches[1]) ) {
            return NULL;
        }
        return $matches[1];
    }
}
