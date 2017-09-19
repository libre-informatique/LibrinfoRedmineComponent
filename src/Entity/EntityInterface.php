<?php

namespace Librinfo\RedmineComponent\Entity;

interface EntityInterface
{
    public function __construct(array $data);
    public function toArray(): array;
}
