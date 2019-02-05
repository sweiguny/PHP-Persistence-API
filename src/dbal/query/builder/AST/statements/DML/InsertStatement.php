<?php

namespace PPA\dbal\statements\DML;

use PPA\core\exceptions\runtime\CollectionStateException;
use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\expressions\Insert;
use PPA\dbal\query\builder\AST\expressions\Into;
use PPA\dbal\query\builder\AST\expressions\sources\Table;
use PPA\dbal\statements\DML\helper\ValuesHelper;
use PPA\dbal\statements\SQLStatement;
use TheSeer\Tokenizer\Exception;

class InsertStatement extends SQLStatement
{
    public function __construct(DriverInterface $driver)
    {
        parent::__construct($driver);
        
        $this->getState()->setStateDirty(CollectionStateException::CODE_STATEMENT_DIRTY, "Only the INSERT part was done now.");
        
        $this->collection[] = new Insert();
    }

    public function intoTable(string $tableName): ValuesHelper
    {
        if ($this->getState()->stateIsClean())
        {
//            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_DIRTY, $this->type, "Method from() can only be called after select().");
            throw new Exception("TODO");
        }
        
//        $this->getState()->setStateClean();
        
        $helper = new ValuesHelper($this->getDriver(), $this);
        
        $this->collection[] = new Into();
        $this->collection[] = new Table($tableName);
        $this->collection[] = $helper;
        
        return $helper;
    }
    
}

?>
