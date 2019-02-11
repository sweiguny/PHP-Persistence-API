<?php

namespace PPA\dbal\query\builder\AST\operators;

class Comparison extends AbstractOperator
{
    const EQUALS         = "=";
    const NOT_EQUALS     = "!=";
    const LOWER_EQUALS   = "<=";
    const GREATER_EQUALS = ">=";
    const LOWER          = "<";
    const GREATER        = ">";
    const BETWEEN        = "BETWEEN";
    const LIKE           = "LIKE";
    
    /**
     *
     * @var string
     */
    private $operator;
    
    public function __construct(string $operator)
    {
        parent::__construct(false);
        
        // TODO: check if operator is allowed
        $this->operator = $operator;
    }

    public function toString(): string
    {
        return $this->operator;
    }

}

?>
