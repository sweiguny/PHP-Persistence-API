<?php

namespace PPA\dbal\query\builder\AST\statements\DML;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\catalogObjects\_Table;
use PPA\dbal\query\builder\AST\clauses\Delete;
use PPA\dbal\query\builder\AST\clauses\From;
use PPA\dbal\query\builder\AST\statements\helper\WhereClauseHelper;
use PPA\dbal\query\builder\AST\statements\SQLStatement;

class DeleteStatement extends SQLStatement
{
    public function __construct(DriverInterface $driver)
    {
        parent::__construct($driver);
        
        $this->collection[] = new Delete();
        $this->collection[] = new From();
    }

    public function fromTable(string $tableName): WhereClauseHelper
    {
        $helper = new WhereClauseHelper($this->getDriver());
        
        $this->collection[] = new _Table($tableName);
        $this->collection[] = $helper;
        
        return $helper;
    }
    
}

?>
