<?php

namespace PPA\dbal\query\builder\AST\statements\helper\traits;

use PPA\dbal\query\builder\AST\catalogObjects\_Field;
use PPA\dbal\query\builder\AST\clauses\GroupBy;
use PPA\dbal\query\builder\AST\statements\helper\HavingClauseHelper;

trait GroupByTrait
{
    public function groupBy(_Field ...$fields): HavingClauseHelper
    {
        $helper = new HavingClauseHelper($this->getDriver());
        
        $this->injectDriversWhereNecessary(...$fields);
        
        $this->collection[] = new GroupBy();
        $this->collection[] = self::consolidateNodes(", ", ...$fields);
        $this->collection[] = $helper;
        
        return $helper;
    }
}

?>
