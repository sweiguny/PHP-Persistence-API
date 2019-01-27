<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Into
 *
 * @author siwe
 */
class Into extends Expression
{
    
    public function toString(): string
    {
        return "INTO";
    }

}

?>
