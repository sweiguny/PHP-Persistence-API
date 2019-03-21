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
        parent::__construct(true);
        
        $this->dataType = DataTypeMapper::mapDatatype($dataType);
        $this->dataType->convertValue($value);
        
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function toString(): string
    {
        switch ($this->dataType->getName())
        {
            case AbstractDatatype::TYPE_STRING:
                return $this->getDriver()->getValueIdentifier() . $this->value . $this->getDriver()->getValueIdentifier();
            default:
                return (string)$this->value;
        }
    }
}

?>
