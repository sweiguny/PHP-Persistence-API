<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Select
 *
 * @author siwe
 */
class From extends Expression
{
    
    public function toString(): string
    {
        return "FROM";
    }

}

?>
