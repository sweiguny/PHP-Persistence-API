<?php

namespace PPA\dbal\statements\DML\helper;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\expressions\FieldReference;
use PPA\dbal\query\builder\AST\expressions\properties\Property;
use PPA\dbal\query\builder\AST\expressions\Set;
use PPA\dbal\statements\DML\UpdateStatement;

class SetClauseHelper extends BaseHelper
{
    /**
     *
     * @var int
     */
    private $count = 0;
    
    /**
     *
     * @var UpdateStatement
     */
    private $parent;

    public function __construct(DriverInterface $driver, UpdateStatement $parent)
    {
        parent::__construct($driver);
        
        $this->parent = $parent;
    }
    
    public function set(string $fieldName): ToHelper
    {
        $this->parent->getState()->setStateClean();
        $this->getState()->setStateDirty(0, "State of set-clause is dirty, because the assignment for field '{$fieldName}' is missing.");
        
        $helper = new ToHelper($this->driver, $this);
        
        if ($this->count++ == 1)
        {
            $this->collection = [$this->consolidateCurrentCollection($this->collection)];
        }
        else
        {
            $this->collection[] = new Set();
        }
        
        $this->collection[] = new FieldReference($fieldName);
        $this->collection[] = $helper;
        
        return $helper;
    }

    /**
     * This method ensures, that after a set clause and the assinment expression
     * the comma follows direct after the field name, so that there's no extra
     * space character before the comma.
     * 
     * @param array $collection
     * @return Property
     */
    private function consolidateCurrentCollection(array $collection): Property
    {
        for ($i = 0, $count = count($collection), $strings = []; $i < $count; $i++)
        {
            $strings[] = $this->workOnElement($collection[$i]);
        }
        
        $wrapper = new class($strings) extends Property {
            private $strings;
            public function __construct(array $strings) {
                 $this->strings = $strings;
            }
            public function toString(): string
            {
                return implode(" ", $this->strings) . ",";
            }
        };
        
        return $wrapper;
    }
    
}

?>
