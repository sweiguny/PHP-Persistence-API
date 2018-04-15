<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Select
 *
 * @author siwe
 */
class Select extends Expression
{
    
    public function toString(): string
    {
        return "SELECT";
    }

}

?>
