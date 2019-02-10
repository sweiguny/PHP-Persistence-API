<?php

namespace PPA\dbal\query\builder\AST\statements\helper\traits;

use PPA\dbal\query\builder\AST\clauses\Where;
use PPA\dbal\query\builder\AST\statements\helper\criteria\WhereCriteriaHelper;

trait WhereTrait
{
    public function where(): WhereCriteriaHelper
    {
        $helper = new WhereCriteriaHelper($this->getDriver());
        
        $this->collection[] = new Where();
        $this->collection[] = $helper;
        
        return $helper;
    }
}

?>
