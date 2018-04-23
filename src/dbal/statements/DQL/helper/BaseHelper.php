<?php

namespace PPA\dbal\statements\DQL\helper;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\clauses\join\Join;
use PPA\dbal\query\builder\AST\expressions\On;
use PPA\dbal\query\builder\AST\expressions\Where;
use PPA\dbal\query\builder\CriteriaBuilder;
use PPA\dbal\statements\DQL\SelectStatement;

/**
 * Description of BaseHelper
 *
 * @author siwe
 */
class BaseHelper
{
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
    private $ASTCollection = [];
    
    public function __construct(DriverInterface $driver)
    {
        $this->driver = $driver;
    }
    
    protected function join(string $joinTable, string $alias = null): Helper2
    {
        $helper2 = new Helper2($this->driver);
        
        $this->ASTCollection[] = new Join($joinTable, $alias);
        $this->ASTCollection[] = $helper2;
        
        return $helper2;
    }
    
    protected function on(): CriteriaBuilder
    {
        $cb = new CriteriaBuilder($this->driver);
        
        $this->ASTCollection[] = new On();
        $this->ASTCollection[] = $cb;
        
        return $cb;
    }
    
    public function orderBy(): Helper2
    {
        
    }
    
    public function groupBy()
    {
        
    }
    

    public function where(): CriteriaBuilder
    {
        $criteriaBuilder = new CriteriaBuilder($this->driver);

        $this->ASTCollection[] = new Where();
        $this->ASTCollection[] = $criteriaBuilder;

        return $criteriaBuilder;
    }

    public function toString(): string
    {
        $collection = $this->ASTCollection;
        
        array_walk($collection, function(&$element) {
            $element = ($element instanceof SelectStatement) ? "({$element->toString()})" : $element->toString();
        });
        
        return implode(" ", $collection);
    }
    
}

?>