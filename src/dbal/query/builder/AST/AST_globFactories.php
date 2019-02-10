<?php

namespace PPA\dbal\query\builder\AST\catalogObjects
{
    function Field(string $name, string $tableReference = null): _Field
    {
        return new _Field($name, $tableReference);
    }
    
    function Table(string $name): _Table
    {
        return new _Table($name);
    }
}
namespace PPA\dbal\query\builder\AST\expressions
{
    function Literal($value, string $dataType = null): _Literal
    {
        return new _Literal($value, $dataType == null ? gettype($value) : $dataType);
    }
    
    function Parameter(string $name = null) // TODO:Define return type, when PHP allows covariant return types
    {
        return $name == null ? new UnnamedParameter() : new NamedParameter($name);
    }
}

namespace PPA\dbal\query\builder\AST\expressions\functions\aggregate
{
    use PPA\dbal\query\builder\AST\expressions\Expression;

    function Sum(Expression $expression): _Sum
    {
        return new _Sum($expression);
    }
    

    function Count(Expression $expression = null): _Count
    {
        return new _Count($expression);
    }
    
}

//namespace PPA\dbal\query\builder\AST\keywords
//{
//    function Distinct(): _Distinct
//    {
//        return new _Distinct();
//    }
//}

namespace PPA\dbal\query\builder\AST\operators
{
    use PPA\dbal\query\builder\AST\expressions\Expression;
    use PPA\dbal\query\builder\AST\expressions\predicates\Predicate;
    use PPA\dbal\query\builder\AST\expressions\predicates\_Between;
    use PPA\dbal\query\builder\AST\expressions\predicates\_InValues;
    use PPA\dbal\query\builder\AST\expressions\predicates\_InSubquery;
    use PPA\dbal\query\builder\AST\statements\DQL\SelectStatement;
    use PPA\dbal\query\builder\AST\SQLDataSource;
    
    function Alias(Expression $expression, string $alias): Expression
    {
        $alias = new _Alias($expression, $alias);
        
        $wrapper = new class($alias) extends Expression implements SQLDataSource
        {
            /**
             *
             * @var _Alias
             */
            private $alias;
            
            public function __construct(_Alias $alias)
            {
                parent::__construct(true);
                
                $this->alias = $alias;
            }
            public function toString(): string
            {
                $this->injectDriversWhereNecessary($this->alias);
                
                return $this->alias->toString();
            }
        };
        
        return $wrapper;
    }
    
    function AsteriskWildcard(): Expression
    {
        $asterisk = new _AsteriskWildcard();
        
        $wrapper = new class($asterisk) extends Expression
        {
            /**
             *
             * @var _AsteriskWildcard
             */
            private $asterisk;
            
            public function __construct(_AsteriskWildcard $asterisk)
            {
                parent::__construct(true);
                
                $this->asterisk = $asterisk;
            }
            public function toString(): string
            {
                return $this->asterisk->toString();
            }
        };
        
        return $wrapper;
    }
    
    function Equals(Expression $left, Expression $right): Predicate
    {
        $operator = new Comparison(Comparison::EQUALS);
        
        return Predicate::makePredicate($left, $right, $operator);
    }
    
    function GreaterEquals(Expression $left, Expression $right): Predicate
    {
        $operator = new Comparison(Comparison::GREATER_EQUALS);
        
        return Predicate::makePredicate($left, $right, $operator);
    }
    
    function LowerEquals(Expression $left, Expression $right): Predicate
    {
        $operator = new Comparison(Comparison::LOWER_EQUALS);
        
        return Predicate::makePredicate($left, $right, $operator);
    }
    
    function Lower(Expression $left, Expression $right): Predicate
    {
        $operator = new Comparison(Comparison::LOWER);
        
        return Predicate::makePredicate($left, $right, $operator);
    }
    
    function Greater(Expression $left, Expression $right): Predicate
    {
        $operator = new Comparison(Comparison::GREATER);
        
        return Predicate::makePredicate($left, $right, $operator);
    }
    
    function Between(Expression $left, Expression $from, Expression $to): _Between
    {
        return new _Between($left, $from, $to);
    }
    
    function InValues(Expression $left, array $right): _InValues
    {
        return new _InValues($left, $right);
    }
    
    function InSubquery(Expression $left, SelectStatement $right): _InSubquery
    {
        return new _InSubquery($left, $right);
    }
    
}

