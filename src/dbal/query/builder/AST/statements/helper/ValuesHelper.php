<?php

namespace PPA\dbal\statements\DML\helper;

use PPA\dbal\query\builder\AST\expressions\properties\Property;
use PPA\dbal\query\builder\AST\expressions\Values;
use PPA\dbal\statements\DQL\SelectStatement;

class ValuesHelper extends SetClauseHelper
{
    
    public function values(Property ...$properties): void
    {
        $this->parent->getState()->setStateClean();
        
        $this->collection[] = new Values(...$properties);
//        $this->collection[] = new Operator(Operator::OPEN_GROUP);
//        $this->collection[] = $this->consolidateProperties($properties);
//        $this->collection[] = new Operator(Operator::CLOSE_GROUP);
    }
    
    /**
     * Alias for $this->query().
     * 
     * @param SelectStatement $subquery
     */
    public function subQuery(SelectStatement $subquery)
    {
        $this->parent->getState()->setStateClean();
        
        $this->collection[] = $subquery;
    }
    
    /**
     * Alias for $this->subQuery().
     * 
     * @param SelectStatement $query
     */
    public function query(SelectStatement $query)
    {
        $this->subQuery($query);
    }
    
//    private function consolidateProperties(array $properties): Property
//    {
//        for ($i = 0, $count = count($properties), $strings = []; $i < $count; $i++)
//        {
//            $strings[] = $this->workOnElement($properties[$i]);
//        }
//        
//        $wrapper = new class($strings) extends Property {
//            private $strings;
//            public function __construct(array $strings) {
//                 $this->strings = $strings;
//            }
//            public function toString(): string
//            {
//                return implode(", ", $this->strings);
//            }
//        };
//        
//        return $wrapper;
//    }
    
}

?>
