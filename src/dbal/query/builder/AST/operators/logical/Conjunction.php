<?php

namespace PPA\dbal\query\builder\AST\operators\logical;

use PPA\dbal\query\builder\AST\operators\Operator;

class Conjunction extends Operator
{
    public function __construct()
    {
        parent::__construct(false);
    }
    
    public function toString(): string
    {
        return "AND";
    }

}

?>
