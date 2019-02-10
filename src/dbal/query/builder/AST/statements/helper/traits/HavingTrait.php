<?php

namespace PPA\dbal\query\builder\AST\statements\helper\traits;

use PPA\dbal\query\builder\AST\clauses\Having;
use PPA\dbal\query\builder\AST\statements\helper\criteria\HavingCriteriaHelper;

trait HavingTrait
{
    public function having(): HavingCriteriaHelper
    {
        $helper = new HavingCriteriaHelper($this->getDriver());
        
        $this->collection[] = new Having();
        $this->collection[] = $helper;
        
        return $helper;
    }
}

?>
