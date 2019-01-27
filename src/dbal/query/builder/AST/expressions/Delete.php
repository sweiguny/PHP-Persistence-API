<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Delete
 *
 * @author siwe
 */
class Delete extends Expression
{
    
    public function toString(): string
    {
        return "DELETE";
    }

}

?>
