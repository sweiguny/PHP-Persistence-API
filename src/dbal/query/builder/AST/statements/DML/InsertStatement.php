<?php

namespace PPA\dbal\query\builder\AST\statements\DML;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\catalogObjects\_Table;
use PPA\dbal\query\builder\AST\clauses\Insert;
use PPA\dbal\query\builder\AST\clauses\Into;
use PPA\dbal\query\builder\AST\statements\helper\ValuesHelper;
use PPA\dbal\query\builder\AST\statements\SQLStatement;

class InsertStatement extends SQLStatement
{
    public function __construct(DriverInterface $driver)
    {
        parent::__construct($driver);
        
//        $this->getState()->setStateDirty(CollectionStateException::CODE_STATEMENT_DIRTY, "Only the INSERT part was done now.");
        
        $this->collection[] = new Insert();
    }

    public function intoTable(string $tableName): ValuesHelper
    {
//        if ($this->getState()->stateIsClean())
//        {
////            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_DIRTY, $this->type, "Method from() can only be called after select().");
//            throw new Exception("TODO");
//        }
        
//        $this->getState()->setStateClean();
        
        $helper = new ValuesHelper($this->getDriver(), $this);
        
        $this->collection[] = new Into();
        $this->collection[] = new _Table($tableName);
        $this->collection[] = $helper;
        
        return $helper;
    }
    
}

?>
