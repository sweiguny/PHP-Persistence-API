<?php

namespace PPA\dbal\query\builder\AST\expressions\predicates;

use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\statements\DQL\SelectStatement;

class _InSubquery extends Predicate
{
    /**
     *
     * @var Expression
     */
    private $left;
    
    /**
     *
     * @var SelectStatement
     */
    private $right;
    
    public function __construct(Expression $left, SelectStatement $right)
    {
        parent::__construct();
        
        $this->left  = $left;
        $this->right = $right;
    }
    
    public function toString(): string
    {
        $this->injectDriversWhereNecessary($this->left/*, $this->right*/); // Since $right is a statement, it must alreay have a driver.
        
        return $this->left->toString() . " IN(" . $this->right->toString() . ")";
    }
}

?>
