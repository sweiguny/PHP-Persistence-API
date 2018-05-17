<?php

namespace PPA\dbal\statements\DQL;

use Exception;
use PPA\core\exceptions\runtime\CollectionStateException;
use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\expressions\From;
use PPA\dbal\query\builder\AST\expressions\properties\Property;
use PPA\dbal\query\builder\AST\expressions\Select;
use PPA\dbal\query\builder\AST\expressions\sources\Table;
use PPA\dbal\query\builder\AST\SQLElementInterface;
use PPA\dbal\statements\DQL\helper\Helper1;

class SelectStatement extends Property
{
    /**
     *
     * @var array
     */
    private $properties;
    
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
    public function __construct(DriverInterface $driver, Property ...$properties)
    {
        parent::__construct();
        
        $this->driver = $driver;
        
        $this->getState()->setStateDirty(CollectionStateException::CODE_STATEMENT_DIRTY, "Only the SELECT part was done now.");
        
        array_unshift($properties, new Select());
        $this->collection = $properties;
        $this->collection[] = new From();
    }

    public function fromTable(string $tableName, string $alias = null): Helper1
    {
        if ($this->getState()->stateIsClean())
        {
//            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_DIRTY, $this->type, "Method from() can only be called after select().");
            throw new Exception("TODO");
        }
        
        $this->getState()->setStateClean();
        
        $helper = new Helper1($this->driver);
        
        $this->collection[] = new Table($tableName, $alias);
        $this->collection[] = $helper;
        
        return $helper;
    }
    
    public function fromSubselect(SelectStatement $stmt): self
    {
        if ($this->getState()->stateIsClean())
        {
//            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_DIRTY, $this->type, "Method from() can only be called after select().");
            throw new Exception("TODO");
        }
        
        $this->getState()->setStateClean();
        
        $this->collection[] = $stmt;
        
        return $this;
    }
    
    protected function workOnElement(SQLElementInterface $element): string
    {
        $string = parent::workOnElement($element);
        
        // In case that $element is an instance of SelectStatement, it is a subquery,
        // which has to be covered in parentheses.
        return ($element instanceof SelectStatement) ? "({$string})" : $string;
    }
}

?>
