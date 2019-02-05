<?php

namespace PPA\dbal\query\builder\AST\expressions;

class UnnamedParameter extends Expression
{
    public function __construct()
    {
        parent::__construct(false);
    }
    
    public function toString(): string
    {
        return "?";
    }
    
}

?>
