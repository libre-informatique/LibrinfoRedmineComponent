<?php

namespace Librinfo\RedmineComponent\IO;

class Csv extends Generic implements IOInterface
{
    /**
     * @var string
     **/
    private $delimiter;
    
    /**
     * @var string
     **/
    private $encodingIn = 'UTF8';
    
    /**
     * @var string
     **/
    private $encodingOut = 'UTF8';
    
    
    public function __construct(string $data, ?array $options = ['delimiter' => ',', 'encodings' => ['UTF8', 'UTF8']])
    {
        parent::__construct($data, $options);
        $this->delimiter = $options['delimiter'];
        $this->encodingIn = $options['encodings'][0];
        $this->encodingOut = $options['encodings'][1];
    }
    
    public function toArray(): array
    {
        $r = [];
        foreach ( explode("\n", $this->get()) as $line ) {
            if ( !$this->hasSameEncodings() ) {
                $line = iconv($this->encodingIn, $this->encodingOut, $line);
            }
            $r[] = str_getcsv($line, $this->delimiter);
        }
        return $r;
    }
    
    private function hasSameEncodings()
    {
        return $this->encodingIn == $this->encodingOut;
    }
}
