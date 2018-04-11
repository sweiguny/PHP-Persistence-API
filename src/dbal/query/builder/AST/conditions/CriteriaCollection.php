<?php

namespace PPA\dbal\query\builder\AST\conditions;

use PPA\core\exceptions\ExceptionFactory;
use PPA\dbal\query\builder\AST\LogicalOperator;
use PPA\dbal\query\builder\AST\SQLElementInterface;
use PPA\dbal\query\builder\CriteriaBuilder;
use PPA\dbal\query\builder\QueryBuilder;

class CriteriaCollection implements SQLElementInterface
{
    const STATE_CLEAN = 0;
    const STATE_DIRTY = -1;
    
    private $ASTCollection = [];
    
    private $parent;
    
    private $state;

    private $expression;
    
    public function __construct(CriteriaBuilder $criteriaBuilder)
    {
        $this->parent = $criteriaBuilder;
        $this->state  = self::STATE_CLEAN;
    }
    
    public function with($expression): Criteria
    {
        if (!$this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is not empty. Therefore please use methods andWith() or orWith().");
        }
        
        return $this->processExpression($expression, self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    public function andWith($expression): Criteria
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method with().");
        }
        
        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        
        return $this->processExpression($expression, self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    public function orWith($expression): Criteria
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method with().");
        }
        
        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::DISJUNCTION);
        
        return $this->processExpression($expression, self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    private function processExpression($expression, int $demandedState, int $newState): Criteria
    {
        if ($this->state != $demandedState)
        {
            throw ExceptionFactory::CollectionState("CriteriaCollection is not in state '{$demandedState}', but is '{$this->state}'.");
        }
        
        $this->state = $newState;
        
        $this->ASTCollection[] = QueryBuilder::processExpression($expression);
        
        $criteria = new Criteria($this);
        
        $this->ASTCollection[] = $criteria;

        return $criteria;
    }

    public function end(): CriteriaBuilder
    {
        if (!$this->stateIsDirty())
        {
            throw ExceptionFactory::CollectionState("CriteriaCollection is not in a dirty state.");
        }
        
        $this->setStateClean();
        
        return $this->parent;
    }
    
    public function export(): array
    {
        return $this->ASTCollection;
    }
    
    public function isEmpty(): bool
    {
        return count($this->ASTCollection) == 0;
    }
    
    public function stateIsDirty(): bool
    {
        return $this->state == self::STATE_DIRTY;
    }
    
    public function stateIsClean(): bool
    {
        return $this->state == self::STATE_CLEAN;
    }
    
    protected function setStateDirty(): void
    {
        $this->state = self::STATE_DIRTY;
    }
    
    protected function setStateClean(): void
    {
        $this->state = self::STATE_CLEAN;
    }

    public function toString(): string
    {
        $string = "";
        
//        foreach ($this->ASTCollection as $element)
//        {
//            $string .= $element->toString();
//        }
        array_walk($this->ASTCollection, function(&$element) {
            $element = $element->toString();
        });
//            print_r($select);
        $string .= implode(" ", $this->ASTCollection);
        
        return $string;
    }
    
}

?>
