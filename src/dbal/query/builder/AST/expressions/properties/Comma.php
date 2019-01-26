<?php

namespace PPA\dbal\query\builder\AST\expressions\properties;

class Comma extends Property
{
    
    public function __construct()
    {
        // To omit $alias parameter from Superclass.
    }
    
    public function toString(): string
    {
        return ",";
    }

}

?>
