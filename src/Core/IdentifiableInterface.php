<?php

namespace Librinfo\RedmineComponent\Core;

interface IdentifiableInterface
{
    public static function getIdentifier(): string;
    public function getIdentifierValue();
}
