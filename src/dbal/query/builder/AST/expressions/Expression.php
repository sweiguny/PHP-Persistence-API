<?php

namespace PPA\dbal\query\builder\AST\expressions;

use PPA\dbal\query\builder\AST\ASTNode;

abstract class Expression extends ASTNode
{
    public function __construct(bool $needsDriver)
    {
        parent::__construct($needsDriver);
    }
    
}

?>
