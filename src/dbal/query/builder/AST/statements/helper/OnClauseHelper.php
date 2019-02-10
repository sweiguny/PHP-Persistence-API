<?php

namespace PPA\dbal\query\builder\AST\statements\helper;

use PPA\dbal\query\builder\AST\clauses\On;
use PPA\dbal\query\builder\AST\statements\helper\criteria\OnCriteriaHelper;

class OnClauseHelper extends WhereClauseHelper
{

    public function on(): OnCriteriaHelper
    {
        $helper = new OnCriteriaHelper($this->getDriver());
        
        $this->collection[] = new On();
        $this->collection[] = $helper;
        
        return $helper;
    }
    
}

?>
