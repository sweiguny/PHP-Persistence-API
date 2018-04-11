<?php

namespace PPA\dbal\query\builder;

use PPA\core\exceptions\ExceptionFactory;
use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\conditions\Criteria;
use PPA\dbal\query\builder\AST\conditions\CriteriaCollection;
use PPA\dbal\query\builder\AST\LogicalOperator;
use PPA\dbal\query\builder\AST\SQLElementInterface;

class CriteriaBuilder implements SQLElementInterface
{
//    const STATE_DIRTY   = -1;
//    const STATE_INITIAL = 0;
//    const STATE_CLEAN   = 1;
    
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
    private $ASTCollection = [];

    private $initialCriteriaCollection;
    private $currentCriteriaCollection;
    
    private $parent;

    public function __construct(DriverInterface $driver, QueryBuilder $queryBuilder)
    {
        $this->driver = $driver;
        $this->parent = $queryBuilder;
        
        $this->initialCriteriaCollection = new CriteriaCollection($this);
//        $this->currentCriteriaCollection = $this->initialCriteriaCollection;
    }
    
    public function with($expression): Criteria
    {
        return $this->initialCriteriaCollection->with($expression);
    }
    
    public function andWith($expression): Criteria
    {
        return $this->initialCriteriaCollection->andWith($expression);
    }
    
    public function orWith($expression): Criteria
    {
        return $this->initialCriteriaCollection->orWith($expression);
    }
    
    public function group(): CriteriaCollection
    {
        if (!$this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is not empty. Therefore please use method andGroup() or orGroup().");
        }
        
        $this->currentCriteriaCollection = new CriteriaCollection($this);

        $this->ASTCollection[] = $this->currentCriteriaCollection;
        
        return $this->currentCriteriaCollection;
    }
    
    
    public function andGroup(): CriteriaCollection
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method group().");
        }
        
        $this->currentCriteriaCollection = new CriteriaCollection($this);

        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::CONJUNCTION);
        $this->ASTCollection[] = $this->currentCriteriaCollection;
        
        return $this->currentCriteriaCollection;
    }
    
    public function orGroup(): CriteriaCollection
    {
        if ($this->isEmpty())
        {
            throw ExceptionFactory::CollectionState("Collection is empty. Therefore please use method group().");
        }
        
        $this->currentCriteriaCollection = new CriteriaCollection($this);

        $this->ASTCollection[] = new LogicalOperator(LogicalOperator::DISJUNCTION);
        $this->ASTCollection[] = $this->currentCriteriaCollection;
        
        return $this->currentCriteriaCollection;
    }
    
    public function end(): QueryBuilder
    {
//        if (!$this->initialCriteriaCollection->isEmpty())
//        {
//            $this->ASTCollection = array_merge($this->initialCriteriaCollection->export(), $this->ASTCollection);
//        }
        
        return $this->parent->end();
    }
    
    public function isEmpty(): bool
    {
        return count($this->ASTCollection) == 0;
    }

    public function toString(): string
    {
        $string = "";
        
        if (!$this->initialCriteriaCollection->isEmpty())
        {
            $this->ASTCollection = array_merge($this->initialCriteriaCollection->export(), $this->ASTCollection);
        }
        
//        var_dump($this->ASTCollection);
        
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
