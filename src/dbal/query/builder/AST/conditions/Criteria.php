<?php

namespace PPA\dbal\query\builder\AST\conditions;

use PPA\dbal\query\builder\AST\expressions\NamedParameter;
use PPA\dbal\query\builder\AST\expressions\properties\Literal;
use PPA\dbal\query\builder\AST\expressions\UnnamedParameter;
use PPA\dbal\query\builder\AST\LogicalOperator;
use PPA\dbal\query\builder\AST\Operator;
use PPA\dbal\query\builder\AST\SQLElementInterface;
use PPA\dbal\query\builder\CriteriaBuilder;
use PPA\dbal\query\builder\QueryBuilder;
use PPA\dbal\statement\SelectStatement;

class Criteria implements SQLElementInterface
{
    
    private $ASTCollection = [];
    private $parent;

    public function __construct(CriteriaBuilder $parent)
    {
        $this->parent = $parent;
    }

    public function not(): self
    {
        
    }
    
    public function betweenParameter(string $name = null): Between
    {
        $between = new Between($this);
        
        $this->ASTCollection[] = new Operator(Operator::BETWEEN);
        $this->ASTCollection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->ASTCollection[] = $between;
        
        return $between;
    }
    
    public function betweenLiteral($from): Between
    {
        $between = new Between($this);
        
        $this->ASTCollection[] = new Operator(Operator::BETWEEN);
        $this->ASTCollection[] = new Literal($from, gettype($from));
        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->ASTCollection[] = $between;
        
        return $between;
    }
    
    public function betweenSubquery(SelectStatement $from): Between
    {
        $between = new Between($this);
        
        $this->ASTCollection[] = new Operator(Operator::BETWEEN);
        $this->ASTCollection[] = $from;
        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->ASTCollection[] = $between;
        
        return $between;
    }
    
    public function equals($expression): CriteriaBuilder
    {
        $this->ASTCollection[] = new Operator(Operator::EQUALS);
        $this->ASTCollection[] = QueryBuilder::processExpression($expression);
        
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
        
        
        $this->ASTCollection[] = new InLiterals($literals);
        
        return $this->end();
    }
    
    public function inSubquery(SelectStatement $subquery): CriteriaBuilder
    {
        $this->ASTCollection[] = new InSubquery($subquery);
        
        return $this->end();
    }

    public function toString(): string
    {
        $string = "";
        
        array_walk($this->ASTCollection, function(&$element) {
            $element = $element->toString();
        });
//            print_r($select);
        $string .= implode(" ", $this->ASTCollection);
        
        return $string;
    }
    
    public function end(): CriteriaBuilder
    {
        $this->parent->setStateClean();
        
        return $this->parent;
    }
    
}

?>
