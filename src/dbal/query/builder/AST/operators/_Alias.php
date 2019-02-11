<?php

namespace PPA\dbal\query\builder\AST\operators;

use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\SQLDataSource;

class _Alias extends AbstractOperator implements SQLDataSource
{
    /**
     *
     * @var string
     */
    private $alias;
    
    /**
     *
     * @var Expression 
     */
    private $expression;

    public function __construct(Expression $expression, string $alias)
    {
        parent::__construct(true);
        
        $this->expression = $expression;
        $this->alias      = $alias;
    }

    public function toString(): string
    {
        $this->injectDriversWhereNecessary($this->expression);
        
        return $this->expression->toString() . " AS " . $this->alias;
    }

}

?>
