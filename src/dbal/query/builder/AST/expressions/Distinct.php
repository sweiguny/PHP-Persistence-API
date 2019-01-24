<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 *
 * @author siwe
 */
class Distinct extends Expression
{
    
    public function toString(): string
    {
        return "DISTINCT";
    }

}

?>
