<?php

namespace PPA\dbal\query\builder\AST\conditions;

use PPA\dbal\query\builder\AST\ASTCollection;
use PPA\dbal\query\builder\AST\expressions\FieldReference;
use PPA\dbal\query\builder\AST\expressions\NamedParameter;
use PPA\dbal\query\builder\AST\expressions\properties\Literal;
use PPA\dbal\query\builder\AST\expressions\UnnamedParameter;
use PPA\dbal\query\builder\AST\LogicalOperator;
use PPA\dbal\query\builder\AST\Operator;
use PPA\dbal\query\builder\CriteriaBuilder;
use PPA\dbal\statement\SelectStatement;

class Criteria extends ASTCollection
{
    
    private $parent;

    public function __construct(CriteriaBuilder $parent)
    {
        parent::__construct();
        
        $this->parent = $parent;
    }

    public function not(): self
    {
        
    }
    
    public function betweenParameter(string $name = null): Between
    {
        $between = new Between($this);
        
        $this->collection[] = new Operator(Operator::BETWEEN);
        $this->collection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        $this->collection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->collection[] = $between;
        
        return $between;
    }
    
    public function betweenLiteral($from): Between
    {
        $between = new Between($this);
        
        $this->collection[] = new Operator(Operator::BETWEEN);
        $this->collection[] = new Literal($from, gettype($from));
        $this->collection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->collection[] = $between;
        
        return $between;
    }
    
    public function betweenSubquery(SelectStatement $from): Between
    {
        $between = new Between($this);
        
        $this->collection[] = new Operator(Operator::BETWEEN);
        $this->collection[] = $from;
        $this->collection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->collection[] = $between;
        
        return $between;
    }
    
    public function equalsParameter(string $name = null): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::EQUALS);
        $this->collection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->end();
    }
    
    public function equalsField(string $fieldName, string $tableOrAliasIndicator = null): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::EQUALS);
        $this->collection[] = new FieldReference($fieldName, $tableOrAliasIndicator);
        
        return $this->end();
    }
    
    public function equalsLiteral($literal): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::EQUALS);
        $this->collection[] = new Literal($literal, gettype($literal));
        
        return $this->end();
    }
    
    public function equalsSubquery(SelectStatement $query): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::EQUALS);
        $this->collection[] = $query;
        
        return $this->end();
    }
    
    public function lowerEquals($expression): CriteriaBuilder
    {
        
    }
    
    public function greaterEquals($expression): CriteriaBuilder
    {
        
    }
    
    public function inLiterals(array $literals): CriteriaBuilder
    {
        foreach ($literals as &$literal)
        {
            $literal = new Literal($literal, gettype($literal));
        }
        
        
        $this->collection[] = new InLiterals($literals);
        
        return $this->end();
    }
    
    public function inSubquery(SelectStatement $subquery): CriteriaBuilder
    {
        $this->collection[] = new InSubquery($subquery);
        
        return $this->end();
    }
    
    public function end(): CriteriaBuilder
    {
        $this->parent->getState()->setStateClean();
        
        return $this->parent;
    }
    
}

?>
