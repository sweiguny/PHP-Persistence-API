<?php

namespace PPA\dbal\query\builder\AST\statements\helper\traits;

use PPA\dbal\query\builder\AST\catalogObjects\_Field;
use PPA\dbal\query\builder\AST\clauses\OrderBy;
use PPA\dbal\query\builder\AST\statements\helper\LimitClauseHelper;

trait OrderByTrait
{
    public function orderBy(_Field ...$fields): LimitClauseHelper
    {
        $helper = new LimitClauseHelper($this->getDriver());
        
        $this->injectDriversWhereNecessary(...$fields);
        
        $this->collection[] = new OrderBy();
        $this->collection[] = self::consolidateNodes(", ", ...$fields);
        $this->collection[] = $helper;
        
        return $helper;
    }
}

?>
