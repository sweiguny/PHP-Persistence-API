<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Insert
 *
 * @author siwe
 */
class Insert extends Expression
{
    
    public function toString(): string
    {
        return "INSERT";
    }

}

?>
