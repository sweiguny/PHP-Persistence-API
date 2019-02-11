<?php

namespace PPA\dbal\query\builder\AST\expressions;

use PPA\dbal\query\builder\AST\operators\Operator;

class _NullValue extends Expression
{
    public function __construct()
    {
        parent::__construct(false);
    }
    
    public function toString(): string
    {
        return Operator::NULL;
    }

}

?>
