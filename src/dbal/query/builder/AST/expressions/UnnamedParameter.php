<?php

namespace PPA\dbal\query\builder\AST\expressions;

class UnnamedParameter extends Expression
{
    
    public function toString(): string
    {
        return "?";
    }

}

?>
