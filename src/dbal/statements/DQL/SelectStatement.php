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
use PPA\dbal\statements\SQLStatement;

class SelectStatement extends SQLStatement
{
    
    public function __construct(DriverInterface $driver, Property ...$properties)
    {
        parent::__construct($driver);
        
        $this->getState()->setStateDirty(CollectionStateException::CODE_STATEMENT_DIRTY, "Only the SELECT part was done now.");
        
        $this->collection[] = new Select();
        $this->collection[] = $this->prepareProperties($properties);
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
        
        $helper = new Helper1($this->getDriver());
        
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
    
    /**
     * This method does consolidate all properties that are to select and
     * wraps an anonymous property class over the outcoming string, so that the
     * ASTCollection can process it (naked strings are not allowed).
     * 
     * @param array $properties
     * @return Property
     */
    private function prepareProperties(array $properties): Property
    {
        for ($i = 0, $count = count($properties), $strings = []; $i < $count; $i++)
        {
            $strings[] = $this->workOnElement($properties[$i]);
        }
        
        $wrapper = new class($strings) extends Property {
            private $strings;
            public function __construct(array $strings) {
                 $this->strings = $strings;
            }
            public function toString(): string
            {
                return implode(", ", $this->strings);
            }
        };
        
        return $wrapper;
    }
    
}

?>
