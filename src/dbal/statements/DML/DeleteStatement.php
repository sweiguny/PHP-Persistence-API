<?php

namespace PPA\dbal\statements\DML;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\expressions\Delete;
use PPA\dbal\query\builder\AST\expressions\From;
use PPA\dbal\query\builder\AST\expressions\sources\Table;
use PPA\dbal\statements\DQL\helper\BaseHelper;
use PPA\dbal\statements\SQLStatement;

class DeleteStatement extends SQLStatement
{
    public function __construct(DriverInterface $driver)
    {
        parent::__construct($driver);
        
//        $this->getState()->setStateDirty(CollectionStateException::CODE_STATEMENT_DIRTY, "Only the DELETE part was done now.");
        
        $this->collection[] = new Delete();
        $this->collection[] = new From();
    }

    public function fromTable(string $tableName): BaseHelper
    {
//        if ($this->getState()->stateIsClean())
//        {
////            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_DIRTY, $this->type, "Method from() can only be called after select().");
//            throw new Exception("TODO");
//        }
        
//        $this->getState()->setStateClean();
        
        $helper = new BaseHelper($this->getDriver());
        
        $this->collection[] = new Table($tableName);
        $this->collection[] = $helper;
        
        return $helper;
    }
    
}

?>
