<?php

namespace PPA\dbal\query\builder\AST\expressions;

use PPA\dbal\query\builder\AST\expressions\properties\Property;
use PPA\dbal\query\builder\AST\Operator;

/**
 * Description of Values
 *
 * @author siwe
 */
class Values extends Expression
{
    /**
     *
     * @var array
     */
    private $properties;

    public function __construct(Property ...$properties)
    {
        $this->properties = $properties;
    }

    public function toString(): string
    {
        $op1 = new Operator(Operator::OPEN_GROUP);
        $op2 = new Operator(Operator::CLOSE_GROUP);
        
        return "VALUES" . $op1->toString() . $this->consolidateProperties($this->properties)->toString() . $op2->toString();
    }
    
    private function consolidateProperties(array $properties): Property
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
