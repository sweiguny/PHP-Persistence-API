<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Set
 *
 * @author siwe
 */
class Set extends Expression
{
    
    public function toString(): string
    {
        return "SET";
    }

}

?>
