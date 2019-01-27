<?php

namespace PPA\dbal\query\builder\AST\expressions\properties;

class NullValue extends Property
{
    
    public function toString(): string
    {
        return "NULL";
    }
    
}

?>
