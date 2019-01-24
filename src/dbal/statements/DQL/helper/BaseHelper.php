<?php

namespace PPA\dbal\statements\DQL\helper;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\ASTCollection;
use PPA\dbal\query\builder\AST\clauses\join\Join;
use PPA\dbal\query\builder\AST\expressions\On;
use PPA\dbal\query\builder\CriteriaBuilder;

/**
 * Description of BaseHelper
 *
 * @author siwe
 */
class BaseHelper extends ASTCollection
{
    use WhereTrait, GroupByTrait;
    
    /**
     *
     * @var DriverInterface
     */
    private $driver;
    
//    private $collection = [];
    
    public function __construct(DriverInterface $driver)
    {
        parent::__construct();
        
        $this->driver = $driver;
    }
    
    protected function join(string $joinTable, string $alias = null): Helper2
    {
        $helper2 = new Helper2($this->driver);
        
        $this->collection[] = new Join($joinTable, $alias);
        $this->collection[] = $helper2;
        
        return $helper2;
    }
    
    protected function on(): CriteriaBuilder
    {
        $cb = new CriteriaBuilder($this->driver);
        
        $this->collection[] = new On();
        $this->collection[] = $cb;
        
        return $cb;
    }
    
    public function orderBy(): Helper2
    {
        
    }
    
//    public function groupBy(Property ...$properties): Helper3
//    {
//        $helper = new Helper3();
//        
//        array_unshift($properties, $helper);
//        array_unshift($properties, new GroupBy());
//        $this->collection = $properties;
//        
//        return $helper;
//    }
    

//    public function where(): CriteriaBuilder
//    {
//        $criteriaBuilder = new CriteriaBuilder($this->driver);
//
//        $this->collection[] = new Where();
//        $this->collection[] = $criteriaBuilder;
//
//        return $criteriaBuilder;
//    }

//    public function toString(): string
//    {
//        $collection = $this->collection;
//        
////        if (empty($collection))
////        {
////            return "";
////        }
//        
//        array_walk($collection, function(&$element) {
//            $element = ($element instanceof SelectStatement) ? "({$element->toString()})" : $element->toString();
//        });
//        
//        return implode(" ", array_filter($collection));
//    }
    
}

?>