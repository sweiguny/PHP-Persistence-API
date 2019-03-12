<?php

namespace PPA\orm\mapping\types;

class TypeString extends AbstractDatatype
{
    public function __construct()
    {
        parent::__construct(self::TYPE_STRING);
    }

    protected function doConversion(&$value): void
    {
        // Nothing to do here, because we expect a string
    }
    
    public function proveValue($value): bool
    {
        return is_string($value);
    }

}

?>
