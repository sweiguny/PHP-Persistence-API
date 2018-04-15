<?php

namespace PPA\dbal\query\builder\AST\conditions;

use PPA\core\exceptions\ExceptionFactory;
use PPA\dbal\query\builder\AST\expressions\FieldReference;
use PPA\dbal\query\builder\AST\expressions\NamedParameter;
use PPA\dbal\query\builder\AST\expressions\properties\Literal;
use PPA\dbal\query\builder\AST\expressions\UnnamedParameter;
use PPA\dbal\query\builder\AST\LogicalOperator;
use PPA\dbal\query\builder\AST\SQLElementInterface;
use PPA\dbal\query\builder\CriteriaBuilder;

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
    
    public function withParameter(string $name = null): Criteria
    {
        if (!$this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is not empty. Therefore please use methods andWith() or orWith().");
        }
        
        $this->ASTCollection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->postProcess(self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    public function andWithParameter(string $name = null): Criteria
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method with().");
        }
        
        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->ASTCollection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->postProcess(self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    public function orWithParameter(string $name = null): Criteria
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method with().");
        }
        
        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::DISJUNCTION);
        $this->ASTCollection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->postProcess(self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    public function withField(string $fieldName, string $tableOrAliasIndicator = null): Criteria
    {
        if (!$this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is not empty. Therefore please use methods andWith() or orWith().");
        }
        
        $this->ASTCollection[] = new FieldReference($fieldName, $tableOrAliasIndicator);
        
        return $this->postProcess(self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    public function andWithField(string $fieldName, string $tableOrAliasIndicator = null): Criteria
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method with().");
        }
        
        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->ASTCollection[] = new FieldReference($fieldName, $tableOrAliasIndicator);
        
        return $this->postProcess(self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    public function orWithField(string $fieldName, string $tableOrAliasIndicator = null): Criteria
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method with().");
        }
        
        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::DISJUNCTION);
        $this->ASTCollection[] = new FieldReference($fieldName, $tableOrAliasIndicator);
        
        return $this->postProcess(self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    public function withLiteral($literal): Criteria
    {
        if (!$this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is not empty. Therefore please use methods andWith() or orWith().");
        }
        
        $this->ASTCollection[] = new Literal($literal, gettype($literal));
        
        return $this->postProcess(self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    public function andWithLiteral($literal): Criteria
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method with().");
        }
        
        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->ASTCollection[] = new Literal($literal, gettype($literal));
        
        return $this->postProcess(self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    public function orWithLiteral($literal): Criteria
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method with().");
        }
        
        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::DISJUNCTION);
        $this->ASTCollection[] = new Literal($literal, gettype($literal));
        
        return $this->postProcess(self::STATE_CLEAN, self::STATE_DIRTY);
    }
    
    private function postProcess(int $demandedState, int $newState): Criteria
    {
//        if ($this->state != $demandedState)
//        {
//            throw ExceptionFactory::CollectionState("CriteriaCollection is not in state '{$demandedState}', but is '{$this->state}'. Current SQL: " . $this->toString());
//        }
        
        $this->state = $newState;
        $criteria    = new Criteria($this);
        
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
