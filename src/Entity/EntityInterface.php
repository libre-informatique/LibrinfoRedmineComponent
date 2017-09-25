<?php

namespace Librinfo\RedmineComponent\Entity;

use Librinfo\RedmineComponent\Core\IdentifiableInterface;

interface EntityInterface extends IdentifiableInterface
{
    public static function create(array $data);
    public function toArray(): array;
    public function isFulfilled(): bool;
    public function get(string $property);
    public function set(string $property, $value): void;
    public function has(string $property): bool;
}
