<?php

namespace PPA\dbal\query\builder\AST\expressions;

use PPA\orm\mapping\DataTypeMapper;
use PPA\orm\mapping\types\AbstractDatatype;

class _Literal extends Expression
{
    /**
     *
     * @var AbstractDatatype
     */
    private $dataType;

    /**
     *
     * @var mixed
     */
    private $value;
    
    public function __construct($value, string $dataType)
    {
        parent::__construct(false);
        
        $this->dataType = DataTypeMapper::mapDatatype($dataType);
        $this->dataType->convertValue($value);
        
        $this->value = $value;
    }

    public function getValue(bool $quoteValueForQuery = false)
    {
        return $quoteValueForQuery ? $this->dataType->quoteValueForQuery($this->value) : $this->value;
    }

    public function toString(): string
    {
        return $this->getValue(true);
    }
}

?>
