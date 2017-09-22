<?php

namespace Librinfo\RedmineComponent\IO;

class Plain extends Generic implements IOInterface
{
    public function toArray(): array
    {
        return [$this->get()];
    }
}
