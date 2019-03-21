<?php

namespace PPA\dbal\query\builder\AST\statements\helper\traits;

use PPA\dbal\query\builder\AST\clauses\CrossJoin;
use PPA\dbal\query\builder\AST\clauses\FullJoin;
use PPA\dbal\query\builder\AST\clauses\Join;
use PPA\dbal\query\builder\AST\clauses\LeftJoin;
use PPA\dbal\query\builder\AST\clauses\RightJoin;
use PPA\dbal\query\builder\AST\SQLDataSource;
use PPA\dbal\query\builder\AST\statements\helper\OnClauseHelper;

trait JoinTrait
{
    public function join(SQLDataSource $source): OnClauseHelper
    {
        $this->collection[] = new Join();
        
        return $this->addSourceAndHelper($source);
    }
    
    public function leftJoin(SQLDataSource $source): OnClauseHelper
    {
        $this->collection[] = new LeftJoin();
        
        return $this->addSourceAndHelper($source);
    }
    
    public function rightJoin(SQLDataSource $source): OnClauseHelper
    {
        $this->collection[] = new RightJoin();
        
        return $this->addSourceAndHelper($source);
    }
    
    public function fullJoin(SQLDataSource $source): OnClauseHelper
    {
        $this->collection[] = new FullJoin();
        
        return $this->addSourceAndHelper($source);
    }
    
    public function crossJoin(SQLDataSource $source): OnClauseHelper
    {
        $this->collection[] = new CrossJoin();
        
        return $this->addSourceAndHelper($source);
    }
    
    private function addSourceAndHelper(SQLDataSource $source): OnClauseHelper
    {
        $helper = new OnClauseHelper($this->getDriver());
        
        $this->collection[] = $source;
        $this->collection[] = $helper;
        
        return $helper;
    }
}

?>
