<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Select
 *
 * @author siwe
 */
class SUM extends Expression
{
    
    public function toString(): string
    {
        return "SUM";
    }

}

?>
