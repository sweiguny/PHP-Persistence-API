<?php

namespace PPA\dbal\query\builder\AST;

class Operator implements SQLElementInterface
{
    const LIKE           = "LIKE";
    const EQUALS         = "=";
    const NOT_EQUALS     = "!=";
    const LOWER_EQUALS   = "<=";
    const GREATER_EQUALS = ">=";
    const LOWER          = "<";
    const GREATER        = ">";
    
    const BETWEEN = "BETWEEN";
    const IN      = "IN";
    
    private $operator;
    
    public function __construct(string $operator)
    {
        $this->operator = $operator;
    }
    
    public function getOperator(): string
    {
        return $this->operator;
    }

    public function toString(): string
    {
        return $this->operator;
    }

}

?>