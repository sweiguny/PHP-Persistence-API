<?php

namespace PPA\dbal\statements\DML;

use PPA\core\exceptions\runtime\CollectionStateException;
use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\expressions\sources\Table;
use PPA\dbal\query\builder\AST\expressions\Update;
use PPA\dbal\statements\DML\helper\SetClauseHelper;
use PPA\dbal\statements\SQLStatement;

class UpdateStatement extends SQLStatement
{
    public function __construct(DriverInterface $driver)
    {
        parent::__construct($driver);
        
        $this->getState()->setStateDirty(CollectionStateException::CODE_STATEMENT_DIRTY, "Only the UPDATE part was done now.");
        
        $this->collection[] = new Update();
    }

    public function table(string $tableName): SetClauseHelper
    {
        if ($this->getState()->stateIsClean())
        {
//            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_DIRTY, $this->type, "Method from() can only be called after select().");
            throw new Exception("TODO");
        }
        
//        $this->getState()->setStateClean();
        
        $helper = new SetClauseHelper($this->getDriver(), $this);
        
        $this->collection[] = new Table($tableName);
        $this->collection[] = $helper;
        
        return $helper;
    }
    
}

?>
