<?php

namespace PPA\dbal\query\builder\AST\expressions\properties;

use PPA\dbal\query\builder\AST\expressions\Distinct;

class FieldDistinct extends Field
{
    
    public function toString(): string
    {
        return (new Distinct())->toString() . " " . parent::toString();
    }

}

?>
