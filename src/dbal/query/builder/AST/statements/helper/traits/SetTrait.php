<?php

namespace PPA\dbal\query\builder\AST\statements\helper\traits;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\ASTCollection;
use PPA\dbal\query\builder\AST\ASTNode;
use PPA\dbal\query\builder\AST\catalogObjects\_Field;
use PPA\dbal\query\builder\AST\clauses\Set;
use PPA\dbal\query\builder\AST\expressions\Expression;
use PPA\dbal\query\builder\AST\operators\Comparison;

trait SetTrait
{
    /**
     * The counter here tells, whether the collection shall be consolidated.
     * 
     * @var int
     */
    private $count = 0;
    
    public function set(string $fieldName, Expression $to): self
    {
        $this->injectDriversWhereNecessary($to);
        
        if ($this->count++ == 1)
        {
            $this->consolidateCurrentCollection();
        }
        else
        {
            $this->collection[] = new Set();
        }
        
        $this->collection[] = new _Field($fieldName);
        $this->collection[] = new Comparison(Comparison::EQUALS);
        $this->collection[] = $to;
        
        return $this;
    }
    
    private function consolidateCurrentCollection(): void
    {
        $newCollection   = new ASTCollection($this->getDriver());
        $newCollection[] = $this->wrapCollection();
        
        $this->collection = $newCollection;
    }
    
    private function wrapCollection(): ASTNode
    {
        return new class($this->getDriver(), $this->collection) extends ASTNode
        {
            /**
             *
             * @var ASTCollection
             */
            private $collection;

            public function __construct(DriverInterface $driver, ASTCollection $collection)
            {
                parent::__construct(true);
                
                $this->injectDriver($driver);
                $this->collection = $collection;
            }

            public function toString(): string
            {
                return $this->collection->toString() . ",";
            }
        };
    }
}

?>
