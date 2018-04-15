<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Select
 *
 * @author siwe
 */
class Where extends Expression
{
    
    public function toString(): string
    {
        return "WHERE";
    }

}

?>
