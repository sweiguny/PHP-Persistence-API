<?php

namespace PPA\dbal\query\builder;

use Exception;
use PPA\core\exceptions\ExceptionFactory;
use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\conditions\Criteria;
use PPA\dbal\query\builder\AST\expressions\FieldReference;
use PPA\dbal\query\builder\AST\expressions\NamedParameter;
use PPA\dbal\query\builder\AST\expressions\properties\Literal;
use PPA\dbal\query\builder\AST\expressions\UnnamedParameter;
use PPA\dbal\query\builder\AST\LogicalOperator;
use PPA\dbal\query\builder\AST\Operator;
use PPA\dbal\query\builder\AST\SQLElementInterface;
use PPA\dbal\statements\DQL\helper\Helper1;

class CriteriaBuilder implements SQLElementInterface
{
    const STATE_CLEAN = 0;
    const STATE_DIRTY = -1;
    
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    private $parent;
    private $state = self::STATE_CLEAN;
    
    private $ASTCollection = [];

    public function __construct(DriverInterface $driver, CriteriaBuilder $parent = null)
    {
        $this->driver = $driver;
        $this->parent = $parent;
    }
    
    public function group(): CriteriaBuilder
    {
        if (!$this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is not empty. Therefore please use method andGroup() or orGroup().");
        }
        
        $cb = new CriteriaBuilder($this->driver, $this);
        
        $this->ASTCollection[] = new Operator(Operator::OPEN_GROUP);
        $this->ASTCollection[] = $cb;
        
        return $cb;
    }
    
    public function andGroup(): CriteriaBuilder
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method group().");
        }
        
        $cb = new CriteriaBuilder($this->driver, $this);

        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->ASTCollection[] = new Operator(Operator::OPEN_GROUP);
        $this->ASTCollection[] = $cb;
        
        return $cb;
    }
    
    public function orGroup(): CriteriaBuilder
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method group().");
        }
        
        $cb = new CriteriaBuilder($this->driver, $this);

        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::DISJUNCTION);
        $this->ASTCollection[] = new Operator(Operator::OPEN_GROUP);
        $this->ASTCollection[] = $cb;
        
        return $cb;
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
        if ($this->state != $demandedState)
        {
            throw ExceptionFactory::CollectionState("CriteriaCollection is not in state '{$demandedState}', but is '{$this->state}'. Current SQL: " . $this->toString());
        }
        
        $this->state = $newState;
        $criteria    = new Criteria($this);
        
        $this->ASTCollection[] = $criteria;

        return $criteria;
    }

    public function endGroup(): CriteriaBuilder
    {
        if ($this->parent == null)
        {
            throw new Exception("TODO: parent is null");
//            throw ExceptionFactory::CollectionState("CriteriaCollection is not in a dirty state.");
        }
        
        $this->ASTCollection[] = new Operator(Operator::CLOSE_GROUP);
        $this->state = self::STATE_CLEAN;
        
        return $this->parent;
    }
    
    public function end(): Helper1
    {
        if ($this->parent != null)
        {
            throw new Exception("TODO: parent isn't null");
//            throw ExceptionFactory::CollectionState("CriteriaCollection is not in a dirty state.");
        }
        
        $helper = new Helper1($this->driver);
        
        $this->ASTCollection[] = $helper;
        
        return $helper;
    }
    
    public function isEmpty(): bool
    {
        return count($this->ASTCollection) == 0;
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

    public function setStateClean(): void
    {
        $this->state = self::STATE_CLEAN;
    }

    public function setStateDirty(): void
    {
        $this->state = self::STATE_DIRTY;
    }
    
}

?>
