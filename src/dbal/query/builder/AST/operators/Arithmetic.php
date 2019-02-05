<?php

namespace PPA\dbal\query\builder\AST\operators;

class Arithmetic
{
    const DIVISION       = "/";
    const MULTIPLICATION = "*";
    const ADDITION       = "+";
    const SUBSTRACTION   = "-";
    
    /**
     *
     * @var string
     */
    private $operator;
    
    public function __construct(string $operator)
    {
        // TODO: check if operator is allowed
        $this->operator = $operator;
    }

}

?>
