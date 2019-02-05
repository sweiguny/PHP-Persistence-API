<?php

namespace PPA\dbal\query\builder\AST\operators;

class _AsteriskWildcard extends Operator
{
    public function __construct()
    {
        parent::__construct(false);
    }
    
    public function toString(): string
    {
        return "*";
    }

}

?>
