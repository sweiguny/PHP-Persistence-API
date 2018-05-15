<?php

namespace PPA\dbal\query\builder\AST\expressions;

use PPA\dbal\query\builder\AST\ASTCollection;

abstract class Expression extends ASTCollection
{
    public function __construct()
    {
        parent::__construct();
    }
    
}

?>
