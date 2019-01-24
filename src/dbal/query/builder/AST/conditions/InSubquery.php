<?php

namespace PPA\dbal\query\builder\AST\conditions;

use PPA\dbal\query\builder\AST\Operator;
use PPA\dbal\statements\DQL\SelectStatement;

class InSubquery extends InLiterals
{
    
    private $subquery;

    public function __construct(SelectStatement $subquery)
    {
        $this->subquery = $subquery;
    }

    public function toString(): string
    {
        return (new Operator(Operator::IN))->toString() . "(" . $this->subquery->toString() . ")";
    }

}

?>
