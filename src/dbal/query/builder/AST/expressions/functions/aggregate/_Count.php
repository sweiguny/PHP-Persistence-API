<?php

namespace PPA\dbal\query\builder\AST\expressions\functions\aggregate;

use PPA\dbal\query\builder\AST\ASTNode;
use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\operators\AsteriskWildcard;

class _Count extends AggregateFn
{
    /**
     *
     * @var ASTNode
     */
    private $expression;
    
    public function __construct(Expression $expression = null)
    {
        parent::__construct(true);
        
        $this->expression = $expression == null ? new AsteriskWildcard() : $expression;
    }
    
    public function toString(): string
    {
        $this->injectDriversWhereNecessary($this->expression);
        
        return "COUNT({$this->expression->toString()})";
    }
    
}

?>
