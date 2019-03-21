<?php

namespace PPA\dbal\query\builder\AST\statements\helper;

use PPA\dbal\query\builder\AST\catalogObjects\_Field;
use PPA\dbal\query\builder\AST\clauses\Values;
use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\operators\Operator;
use PPA\dbal\query\builder\AST\operators\Parenthesis;
use PPA\dbal\query\builder\AST\statements\DQL\SelectStatement;
use PPA\dbal\query\builder\AST\statements\helper\traits\SetTrait;

class ValuesHelper extends BaseHelper
{
    use SetTrait;
    
    public function values(Expression ...$expressions): void
    {
        $this->injectDriversWhereNecessary(...$expressions);
//        $this->parent->getState()->setStateClean();
        
        $collection = [
            new Values(),
            new Parenthesis(Parenthesis::OPEN),
            self::consolidateNodes(", ", ...$expressions),
            new Parenthesis(Parenthesis::CLOSE)
        ];
        
        $this->collection[] = self::consolidateNodes("", ...$collection);
    }
    
    public function fields(_Field ...$fields): self
    {
        $this->injectDriversWhereNecessary(...$fields);
        
        $collection = [
            new Parenthesis(Parenthesis::OPEN),
            self::consolidateNodes(", ", ...$fields),
            new Parenthesis(Parenthesis::CLOSE)
        ];
        
        $this->collection[] = self::consolidateNodes("", ...$collection);
//        $this->collection[] = new Operator(Operator::ASSIGN);
        
        return $this;
    }
    
    /**
     * Alias for $this->query().
     * 
     * @param SelectStatement $subquery
     */
    public function subQuery(SelectStatement $subquery): void
    {
//        $this->parent->getState()->setStateClean();
        
        $this->collection[] = $subquery;
    }
    
    /**
     * Alias for $this->subQuery().
     * 
     * @param SelectStatement $query
     */
    public function query(SelectStatement $query): void
    {
        $this->subQuery($query);
    }
    
}

?>
