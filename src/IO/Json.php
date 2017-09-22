<?php

namespace Librinfo\RedmineComponent\IO;

class Json extends Generic implements IOInterface
{
    /**
     * @var bool
     **/
    private $assoc;
    
    public function __construct(string $data, ?array $options = ['assoc' => true])
    {
        parent::__construct($data, $options);
        $this->assoc = $options['assoc'];
    }
    
    public function toArray(): array
    {
        return json_decode($this->get(), $this->assoc);
    }
}
