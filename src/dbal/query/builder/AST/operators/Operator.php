<?php

namespace PPA\dbal\query\builder\AST\operators;

class Operator extends AbstractOperator
{
    const ASSIGN = "=";
    const NULL   = "NULL";
    
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
