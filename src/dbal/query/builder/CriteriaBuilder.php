<?php

namespace PPA\dbal\query\builder;

use PPA\core\exceptions\ExceptionFactory;
use PPA\core\exceptions\runtime\CollectionStateException;
use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\ASTCollection;
use PPA\dbal\query\builder\AST\conditions\Criteria;
use PPA\dbal\query\builder\AST\expressions\FieldReference;
use PPA\dbal\query\builder\AST\expressions\NamedParameter;
use PPA\dbal\query\builder\AST\expressions\properties\Literal;
use PPA\dbal\query\builder\AST\expressions\UnnamedParameter;
use PPA\dbal\query\builder\AST\LogicalOperator;
use PPA\dbal\query\builder\AST\Operator;
use PPA\dbal\statements\DQL\helper\Helper1;

class CriteriaBuilder extends ASTCollection
{
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
    /**
     *
     * @var CriteriaBuilder
     */
    private $parent;
    
    public function __construct(DriverInterface $driver, CriteriaBuilder $parent = null)
    {
        parent::__construct();
        
        $this->driver = $driver;
        $this->parent = $parent;
    }
    
    public function group(): CriteriaBuilder
    {
        if (!$this->isEmpty())
        {
            throw ExceptionFactory::CollectionState(CollectionStateException::CODE_NOT_EMPTY, "CriteriaBuilder is not empty. Therefore please use method andGroup() or orGroup().");
        }
        
        $cb = new CriteriaBuilder($this->driver, $this);
        $this->getState()->setStateDirty(CollectionStateException::CODE_GROUP_DIRTY, "Group open and not closed.");
        
//        var_dump(spl_object_hash($this->getState()));
//        var_dump($this->getState()->getState());
        
        $this->collection[] = new Operator(Operator::OPEN_GROUP);
        $this->collection[] = $cb;
        
        return $cb;
    }
    
    public function andGroup(): CriteriaBuilder
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("CriteriaBuilder is empty. Therefore please use method group().");
        }
        
        $cb = new CriteriaBuilder($this->driver, $this);
        $this->getState()->setStateDirty(CollectionStateException::CODE_GROUP_DIRTY, "Group open and not closed.");

        $this->collection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->collection[] = new Operator(Operator::OPEN_GROUP);
        $this->collection[] = $cb;
        
        return $cb;
    }
    
    public function orGroup(): CriteriaBuilder
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState(CollectionStateException::CODE_EMPTY, "CriteriaBuilder is empty. Therefore please use method group().");
        }
        
        $cb = new CriteriaBuilder($this->driver, $this);
        $this->getState()->setStateDirty(CollectionStateException::CODE_GROUP_DIRTY, "Group open and not closed.");
        
        $this->collection[] = new LogicalOperator(LogicalOperator::DISJUNCTION);
        $this->collection[] = new Operator(Operator::OPEN_GROUP);
        $this->collection[] = $cb;
        
        return $cb;
    }
    
    public function withParameter(string $name = null): Criteria
    {
        $this->preProcess(false);
        
        $this->collection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->postProcess();
    }
    
    public function andWithParameter(string $name = null): Criteria
    {
        $this->preProcess(true);
        
        $this->collection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->collection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->postProcess();
    }
    
    public function orWithParameter(string $name = null): Criteria
    {
        $this->preProcess(true);
        
        $this->collection[] = new LogicalOperator(LogicalOperator::DISJUNCTION);
        $this->collection[] = $name == null ? new UnnamedParameter() : new NamedParameter($name);
        
        return $this->postProcess();
    }
    
    public function withField(string $fieldName, string $tableOrAliasIndicator = null): Criteria
    {
        $this->preProcess(false);
        
        $this->collection[] = new FieldReference($fieldName, $tableOrAliasIndicator);
        
        return $this->postProcess();
    }
    
    public function andWithField(string $fieldName, string $tableOrAliasIndicator = null): Criteria
    {
        $this->preProcess(true);
        
        $this->collection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->collection[] = new FieldReference($fieldName, $tableOrAliasIndicator);
        
        return $this->postProcess();
    }
    
    public function orWithField(string $fieldName, string $tableOrAliasIndicator = null): Criteria
    {
        $this->preProcess(true);
        
        $this->collection[] = new LogicalOperator(LogicalOperator::DISJUNCTION);
        $this->collection[] = new FieldReference($fieldName, $tableOrAliasIndicator);
        
        return $this->postProcess();
    }
    
    public function withLiteral($literal): Criteria
    {
        $this->preProcess(false);
        
        $this->collection[] = new Literal($literal, gettype($literal));
        
        return $this->postProcess();
    }
    
    public function andWithLiteral($literal): Criteria
    {
        $this->preProcess(true);
        
        $this->collection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->collection[] = new Literal($literal, gettype($literal));
        
        return $this->postProcess();
    }
    
    public function orWithLiteral($literal): Criteria
    {
        $this->preProcess(true);
        
        $this->collection[] = new LogicalOperator(LogicalOperator::DISJUNCTION);
        $this->collection[] = new Literal($literal, gettype($literal));
        
        return $this->postProcess();
    }
    
    private function preProcess(bool $checkForEmtpiness): void
    {
        if (true == $checkForEmtpiness && $this->isEmpty())
        {
            throw ExceptionFactory::CollectionState(CollectionStateException::CODE_EMPTY, "CriteriaBuilder is empty. Therefore please use one of the methods starting like 'with'.");
        }
        else if (false == $checkForEmtpiness && !$this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("CriteriaBuilder is not empty. Therefore please use one of the methods starting like 'andWith' or 'orWith'.");
        }
        
        
        if ($this->getState()->stateIsDirty())
        {
            throw ExceptionFactory::CollectionState(CollectionStateException::CODE_CRITERIA_DIRTY, "CriteriaBuilder is not in a clean state.");
        }
        
        $this->getState()->setStateDirty(CollectionStateException::CODE_CRITERIA_DIRTY, "Criteria is in process");
    }
    
    private function postProcess(): Criteria
    {
        $criteria = new Criteria($this);
        
        $this->collection[] = $criteria;

        return $criteria;
    }

    public function closeGroup(): CriteriaBuilder
    {
        if ($this->parent == null)
        {
            throw new \Exception("TODO: parent is null");
        }
        
//        var_dump($this->parent->getState()->getState());
//        var_dump(spl_object_hash($this->parent->getState()));
        
        if ($this->parent->getState()->stateIsClean())
        {
            throw ExceptionFactory::CollectionState(CollectionStateException::CODE_CRITERIA_CLEAN, "CriteriaBuilder is not in a dirty state.");
        }
        
        $this->parent->getState()->setStateClean();
        
        $this->collection[] = new Operator(Operator::CLOSE_GROUP);
        
        return $this->parent;
    }
    
    public function end(): Helper1
    {
        if ($this->getState()->stateIsClean())
        {
            throw ExceptionFactory::CollectionState(CollectionStateException::CODE_CRITERIA_CLEAN, "CriteriaBuilder is not in a dirty state.");
        }
        
        $this->getState()->setStateClean();
        
        if ($this->parent != null)
        {
            throw new \Exception("TODO: parent isn't null");
        }
        
        $helper = new Helper1($this->driver);
        
        $this->collection[] = $helper;
        
        return $helper;
    }
    
    public function isEmpty(): bool
    {
        return count($this->collection) == 0;
    }

}

?>
