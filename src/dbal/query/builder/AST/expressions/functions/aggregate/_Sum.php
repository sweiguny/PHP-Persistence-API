<?php

namespace PPA\dbal\query\builder\AST\expressions\functions\aggregate;

use PPA\dbal\query\builder\AST\ASTNode;
use PPA\dbal\query\builder\AST\expressions\Expression;

class _Sum extends AggregateFn
{
    /**
     *
     * @var ASTNode
     */
    private $expression;
    
    public function __construct(Expression $expression)
    {
        parent::__construct(true);
        
        $this->expression = $expression;
    }
    
    public function toString(): string
    {
        $this->injectDriversWhereNecessary($this->expression);
        
        return "SUM({$this->expression->toString()})";
    }

}

?>
