<?php

namespace PPA\dbal\query\builder\AST\statements\helper;

use PPA\dbal\query\builder\AST\clauses\Values;
use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\operators\Parenthesis;
use PPA\dbal\query\builder\AST\statements\DQL\SelectStatement;

class ValuesHelper extends SetClauseHelper
{
    
    public function values(Expression ...$expressions): void
    {
//        $this->parent->getState()->setStateClean();
        
        $collection = [
            new Values(),
            new Parenthesis(Parenthesis::OPEN),
            self::consolidateNodes(", ", ...$expressions),
            new Parenthesis(Parenthesis::CLOSE)
        ];
        
        $this->collection[] = self::consolidateNodes("", ...$collection);
    }
    
    /**
     * Alias for $this->query().
     * 
     * @param SelectStatement $subquery
     */
    public function subQuery(SelectStatement $subquery)
    {
//        $this->parent->getState()->setStateClean();
        
        $this->collection[] = $subquery;
    }
    
    /**
     * Alias for $this->subQuery().
     * 
     * @param SelectStatement $query
     */
    public function query(SelectStatement $query)
    {
        $this->subQuery($query);
    }
    
}

?>
