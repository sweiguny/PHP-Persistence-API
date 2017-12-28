<?php

namespace PPA\orm\mapping\types;

class TypeInteger extends AbstractDatatype
{
    public function __construct()
    {
        parent::__construct(self::TYPE_INTEGER);
    }
    
    protected function doConversion(&$value): void
    {
        settype($value, $this->getName());
    }

    public function proveValue($value): bool
    {
        return ctype_digit(strval($value));
    }

    public function quoteValueForQuery($value): string
    {
        return (string)$value;
    }

}

?>
