<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of Select
 *
 * @author siwe
 */
class Having extends Expression
{
    
    public function toString(): string
    {
        return "HAVING";
    }

}

?>
