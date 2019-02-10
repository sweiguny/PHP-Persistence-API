<?php

namespace PPA\dbal\query\builder\AST\statements\helper\criteria;

use PPA\dbal\query\builder\AST\expressions\predicates\Predicate;
use PPA\dbal\query\builder\AST\operators\logical\Conjunction;
use PPA\dbal\query\builder\AST\operators\logical\Disjunction;
use PPA\dbal\query\builder\AST\operators\Parenthesis;

trait CriteriaTrait
{
    public function group(): self
    {
        $helper = new self($this->getDriver(), $this);
        
        $this->collection[] = new Parenthesis(Parenthesis::OPEN);
        $this->collection[] = $helper;
        $this->collection[] = new Parenthesis(Parenthesis::CLOSE);
        
        return $helper;
    }
    
    public function closeGroup(): self
    {
        return $this->parent;
    }
    
    public function criteria(Predicate $predicate): self
    {
        $this->collection[] = $predicate;
        
        return $this;
    }
    
    public function and(): self
    {
        $this->collection[] = new Conjunction();
        
        return $this;
    }
    
    public function or(): self
    {
        $this->collection[] = new Disjunction();
        
        return $this;
    }
}

?>
