<?php

namespace PPA\dbal\query\builder\AST\conditions;

use PPA\core\exceptions\runtime\CollectionStateException;
use PPA\dbal\query\builder\AST\ASTCollection;
use PPA\dbal\query\builder\AST\expressions\FieldReference;
use PPA\dbal\query\builder\AST\expressions\NamedParameter;
use PPA\dbal\query\builder\AST\expressions\properties\Literal;
use PPA\dbal\query\builder\AST\expressions\UnnamedParameter;
use PPA\dbal\query\builder\AST\LogicalOperator;
use PPA\dbal\query\builder\AST\Operator;
use PPA\dbal\query\builder\CriteriaBuilder;
use PPA\dbal\statements\DQL\SelectStatement;

class Criteria extends ASTCollection
{
    
    private $parent;

    public function __construct(CriteriaBuilder $parent)
    {
        parent::__construct();
        
        $this->parent = $parent;
        
        $this->getState()->setStateDirty(CollectionStateException::CODE_CRITERIA_DIRTY, "Condition unfinished.");
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
    
    public function lowerEqualsParameter(string $name = null): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::LOWER_EQUALS);
        $this->collection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->end();
    }
    
    public function lowerEqualsField(string $fieldName, string $tableOrAliasIndicator = null): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::LOWER_EQUALS);
        $this->collection[] = new FieldReference($fieldName, $tableOrAliasIndicator);
        
        return $this->end();
    }
    
    public function lowerEqualsLiteral($literal): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::LOWER_EQUALS);
        $this->collection[] = new Literal($literal, gettype($literal));
        
        return $this->end();
    }
    
    public function lowerEqualsSubquery(SelectStatement $query): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::LOWER_EQUALS);
        $this->collection[] = $query;
        
        return $this->end();
    }
    
    public function greaterEqualsParameter(string $name = null): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::GREATER_EQUALS);
        $this->collection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->end();
    }
    
    public function greaterEqualsField(string $fieldName, string $tableOrAliasIndicator = null): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::GREATER_EQUALS);
        $this->collection[] = new FieldReference($fieldName, $tableOrAliasIndicator);
        
        return $this->end();
    }
    
    public function greaterEqualsLiteral($literal): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::GREATER_EQUALS);
        $this->collection[] = new Literal($literal, gettype($literal));
        
        return $this->end();
    }
    
    public function greaterEqualsSubquery(SelectStatement $query): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::GREATER_EQUALS);
        $this->collection[] = $query;
        
        return $this->end();
    }
    
    public function greaterParameter(string $name = null): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::GREATER);
        $this->collection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->end();
    }
    
    public function greaterField(string $fieldName, string $tableOrAliasIndicator = null): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::GREATER);
        $this->collection[] = new FieldReference($fieldName, $tableOrAliasIndicator);
        
        return $this->end();
    }
    
    public function greaterLiteral($literal): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::GREATER);
        $this->collection[] = new Literal($literal, gettype($literal));
        
        return $this->end();
    }
    
    public function greaterSubquery(SelectStatement $query): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::GREATER);
        $this->collection[] = $query;
        
        return $this->end();
    }
    
    public function lowerParameter(string $name = null): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::LOWER);
        $this->collection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->end();
    }
    
    public function lowerField(string $fieldName, string $tableOrAliasIndicator = null): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::LOWER);
        $this->collection[] = new FieldReference($fieldName, $tableOrAliasIndicator);
        
        return $this->end();
    }
    
    public function lowerLiteral($literal): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::LOWER);
        $this->collection[] = new Literal($literal, gettype($literal));
        
        return $this->end();
    }
    
    public function lowerSubquery(SelectStatement $query): CriteriaBuilder
    {
        $this->collection[] = new Operator(Operator::LOWER);
        $this->collection[] = $query;
        
        return $this->end();
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
        
        $this->getState()->setStateClean();
        $this->parent->getState()->setStateClean();
        
        return $this->parent;
    }
    
}

?>
