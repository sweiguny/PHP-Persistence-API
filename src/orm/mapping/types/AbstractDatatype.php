<?php

namespace PPA\orm\mapping\types;

use PPA\core\exceptions\ExceptionFactory;

abstract class AbstractDatatype
{
    const TYPE_STRING  = "string";
    const TYPE_INTEGER = "integer";
    
    /**
     *
     * @var string
     */
    private $name;
    
    public function __construct(string $name)
    {
        $this->name = $name;
    }
    
    public function convertValue(&$value)
    {
        if (!$this->proveValue($value))
        {
            throw ExceptionFactory::InvalidArgument("Value '{$value}' is not type {$this->getName()}, but is " . gettype($value) . ".");
        }
        
        $this->doConversion($value);
    }
    
    public abstract function quoteValueForQuery($value): string;

    public abstract function proveValue($value): bool;
    
    protected abstract function doConversion(&$value): void;
    
    public function getName(): string
    {
        return $this->name;
    }
    
}

?>
