<?php

namespace PPA\dbal\query\builder\AST\expressions\functions\aggregate;

use PPA\dbal\query\builder\AST\expressions\functions\FunctionClass;

abstract class AggregateFn extends FunctionClass
{
    public function __construct(bool $needsDriver)
    {
        parent::__construct($needsDriver);
    }
    
}

?>
