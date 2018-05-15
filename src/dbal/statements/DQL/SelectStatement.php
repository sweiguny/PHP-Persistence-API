<?php

namespace PPA\dbal\statements\DQL;

use Exception;
use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\expressions\From;
use PPA\dbal\query\builder\AST\expressions\properties\Property;
use PPA\dbal\query\builder\AST\expressions\Select;
use PPA\dbal\query\builder\AST\expressions\sources\Table;
use PPA\dbal\statements\DQL\helper\Helper1;


class SelectStatement extends Property
{
    private $properties;
    
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
//    private $collection = [];

    private $state;
    
    public function __construct(DriverInterface $driver, Property ...$properties)
    {
        parent::__construct();
        
        $this->driver = $driver;
//        $this->state  = self::STATE_DIRTY;
        
        $this->getState()->setStateDirty("Only the SELECT part was done now.");
        
        array_unshift($properties, new Select());
        $this->collection = $properties;
        $this->collection[] = new From();
    }

    public function fromTable(string $tableName, string $alias = null): Helper1
    {
//        if ($this->state != self::STATE_DIRTY)
        if ($this->getState()->stateIsClean())
        {
//            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_DIRTY, $this->type, "Method from() can only be called after select().");
            throw new Exception("TODO");
        }
        
//        $this->state = self::STATE_CLEAN;
        $this->getState()->setStateClean();
        $helper      = new Helper1($this->driver);
        
        $this->collection[] = new Table($tableName, $alias);
        $this->collection[] = $helper;
        
        return $helper;
    }
    
    public function fromSubselect(SelectStatement $stmt): self
    {
//        if ($this->state != self::STATE_DIRTY)
        if ($this->getState()->stateIsClean())
        {
//            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_DIRTY, $this->type, "Method from() can only be called after select().");
            throw new Exception("TODO");
        }
        
//        $this->state = self::STATE_CLEAN;
        $this->getState()->setStateClean();
        $this->collection[] = $stmt;
        
        return $this;
    }
    
//    public function toString(): string
//    {
////        if ($this->state != self::STATE_CLEAN)
//        if ($this->stateIsDirty())
//        {
//            throw new Exception("TODO");
//        }
//        
//        return parent::toString();
//    }

    protected function workOnElement(\PPA\dbal\query\builder\AST\SQLElementInterface $element): string
    {
        return ($element instanceof SelectStatement) ? "(" . parent::workOnElement($element) . ")" : parent::workOnElement($element);
    }
}

?>
