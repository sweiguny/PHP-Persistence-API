<?php

namespace PPA\dbal\query\builder\AST\statements\helper;

use PPA\dbal\query\builder\AST\clauses\On;

class OnClauseHelper extends BaseHelper
{

    public function on(): CriteriaHelper
    {
        $helper = new CriteriaHelper($this->getDriver());
        
        $this->collection[] = new On();
        $this->collection[] = $helper;
        
        return $helper;
    }
    
}

?>
