<?php

namespace PPA\dbal\query\builder\AST\statements\helper;

use PPA\dbal\query\builder\AST\clauses\Join;
use PPA\dbal\query\builder\AST\SQLDataSource;

class JoinClauseHelper extends WhereClauseHelper
{
    
    public function join(SQLDataSource $source)
    {
        $helper = new OnClauseHelper($this->getDriver());
        
        $this->collection[] = new Join();
        $this->collection[] = $source;
        $this->collection[] = $helper;
        
        return $helper;
    }
    
}

?>
