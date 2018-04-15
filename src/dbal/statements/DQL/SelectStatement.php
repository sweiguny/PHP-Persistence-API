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
    const STATE_DIRTY = -1;
    const STATE_CLEAN = 1;
    
    private $properties;
    
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
    private $ASTCollection = [];

    private $state;
    
    public function __construct(DriverInterface $driver, Property ...$properties)
    {
        $this->driver = $driver;
        $this->state  = self::STATE_DIRTY;
        
        array_unshift($properties, new Select());
        $this->ASTCollection = $properties;
        $this->ASTCollection[] = new From();
    }

    public function fromTable(string $tableName, string $alias = null): Helper1
    {
        if ($this->state != self::STATE_DIRTY)
        {
//            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_DIRTY, $this->type, "Method from() can only be called after select().");
            throw new Exception("TODO");
        }
        
        $this->state = self::STATE_CLEAN;
        $helper      = new Helper1($this->driver);
        
        $this->ASTCollection[] = new Table($tableName, $alias);
        $this->ASTCollection[] = $helper;
        
        return $helper;
    }
    
    public function fromSubselect(SelectStatement $stmt): self
    {
        if ($this->state != self::STATE_DIRTY)
        {
//            throw ExceptionFactory::InvalidQueryBuilderState(self::STATE_DIRTY, $this->type, "Method from() can only be called after select().");
            throw new Exception("TODO");
        }
        
        $this->state = self::STATE_CLEAN;
        $this->ASTCollection[] = $stmt;
        
        return $this;
    }
    
    public function toString(): string
    {
        if ($this->state != self::STATE_CLEAN)
        {
            throw new Exception("TODO");
        }
        
        $collection = $this->ASTCollection;
        
        array_walk($collection, function(&$element) {
            $element = ($element instanceof SelectStatement) ? "({$element->toString()})" : $element->toString();
        });
        
        return implode(" ", $collection);
    }

}

?>
