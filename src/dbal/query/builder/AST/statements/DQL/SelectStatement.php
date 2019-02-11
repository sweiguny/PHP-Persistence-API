<?php

namespace PPA\dbal\query\builder\AST\statements\DQL;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\ASTCollection;
use PPA\dbal\query\builder\AST\clauses\From;
use PPA\dbal\query\builder\AST\clauses\Select;
use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\keywords\Keyword;
use PPA\dbal\query\builder\AST\operators\_AsteriskWildcard;
use PPA\dbal\query\builder\AST\SQLDataSource;
use PPA\dbal\query\builder\AST\statements\helper\JoinClauseHelper;
use PPA\dbal\query\builder\AST\statements\SQLStatement;

class SelectStatement extends SQLStatement implements SQLDataSource
{
    public function __construct(DriverInterface $driver, ?Keyword $keyword, Expression ...$selectList)
    {
        parent::__construct($driver);
        
        if (empty($selectList))
        {
            $selectList[] = new _AsteriskWildcard();
        }
        else
        {
            $this->injectDriversWhereNecessary(...$selectList);
        }
//        $this->getState()->setStateDirty(CollectionStateException::CODE_STATEMENT_DIRTY, "Only the SELECT part was done now.");
        
        $this->collection[] = new Select();
        
        if ($keyword != null)
        {
            $this->collection[] = $keyword;
        }
        
        $this->collection[] = self::consolidateNodes(", ", ...$selectList);
    }

    public function from(SQLDataSource ...$sources): JoinClauseHelper
    {
        $this->injectDriversWhereNecessary(...$sources);
        
        $helper = new JoinClauseHelper($this->getDriver());
        
        $this->collection[] = new From();
        $this->collection[] = self::consolidateNodes(", ", ...$sources);
        $this->collection[] = $helper;
        
        return $helper;
    }
    
}

?>
