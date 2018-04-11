<?php

namespace PPA\dbal\query\builder\AST;

class LogicalOperator extends Operator
{
    const CONJUNCTION = "AND";
    const DISJUNCTION = "OR";
    const NEGATION    = "NOT";
    
    private $operator;
    
//    public function __construct(string $operator)
//    {
//        $this->operator = $operator;
//    }
    
    public function getOperator(): string
    {
        return $this->operator;
    }

}

?>
