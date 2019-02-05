<?php

namespace PPA\dbal\query\builder\AST\statements;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\ASTNode;

abstract class SQLStatement extends ASTNode
{
    public function __construct(DriverInterface $driver)
    {
        parent::__construct(true);
        
        $this->injectDriver($driver);
    }

}

?>
