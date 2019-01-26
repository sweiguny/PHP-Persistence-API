<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Update
 *
 * @author siwe
 */
class Update extends Expression
{
    
    public function toString(): string
    {
        return "UPDATE";
    }

}

?>
