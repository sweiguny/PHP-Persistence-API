<?php

namespace PPA\dbal\query\builder\AST\expressions\properties;

use PPA\dbal\query\builder\AST\expressions\SUM;

class FieldSUM extends Field
{
    
    public function toString(): string
    {
        return (new SUM())->toString() . "(" . parent::toString() . ")";
    }

}

?>
