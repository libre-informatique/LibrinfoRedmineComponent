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
}
