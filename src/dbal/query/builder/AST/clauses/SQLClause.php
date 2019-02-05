<?php

namespace PPA\dbal\query\builder\AST\clauses;

use PPA\dbal\query\builder\AST\ASTNode;

abstract class SQLClause extends ASTNode
{
    public function __construct()
    {
        parent::__construct(false);
    }
}

?>
