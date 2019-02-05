<?php

namespace PPA\dbal\query\builder\AST\statements\helper;

use PPA\dbal\query\builder\AST\clauses\Where;

class WhereClauseHelper extends BaseHelper
{
    
    public function where(): CriteriaHelper
    {
        $helper = new CriteriaHelper($this->getDriver());
        
        $this->collection[] = new Where();
        $this->collection[] = $helper;
        
        return $helper;
    }
    
}

?>
