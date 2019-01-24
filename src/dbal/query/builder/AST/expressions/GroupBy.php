<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Select
 *
 * @author siwe
 */
class GroupBy extends Expression
{
    
    public function toString(): string
    {
        return "GROUP BY";
    }

}

?>
