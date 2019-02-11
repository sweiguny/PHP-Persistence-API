<?php

namespace PPA\dbal\query\builder\AST\expressions\predicates;

use PPA\dbal\query\builder\AST\ASTCollection;
use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\operators\AbstractOperator;
use PPA\dbal\query\builder\AST\operators\Operator;

abstract class Predicate extends Expression
{
    /**
     *
     * @var ASTCollection
     */
    protected $collection;
    
    public function __construct()
    {
        parent::__construct(true);
        
        $this->collection = new ASTCollection();
    }
    
    public function toString(): string
    {
        $this->injectDriversWhereNecessary($this->collection);

        return $this->collection->toString();
    }
    
    public static function makePredicate(Expression $left, Expression $right, AbstractOperator $operator): Predicate
    {
        $predicate = new class($left, $right, $operator) extends Predicate
        {
            /**
             *
             * @var Expression
             */
//            private $left, $right;
            
            /**
             *
             * @var Operator
             */
//            private $operator;
            public function __construct(Expression $left, Expression $right, AbstractOperator $operator)
            {
                parent::__construct();
                
//                $this->left     = $left;
//                $this->right    = $right;
//                $this->operator = $operator;
                $this->collection[] = $left;
                $this->collection[] = $operator;
                $this->collection[] = $right;
            }
        };
        
        return $predicate;
    }
}

?>
