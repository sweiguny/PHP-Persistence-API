<?php

namespace PPA\dbal\statements\DQL\helper;

use PPA\dbal\query\builder\AST\expressions\GroupBy;
use PPA\dbal\query\builder\AST\expressions\properties\Property;

trait GroupByTrait
{
    public function groupBy(Property ...$properties): Helper3
    {
        $helper = new Helper3($this->driver);
        
        $this->collection[] = new GroupBy();
        
        foreach ($properties as $property)
        {
            $this->collection[] = $property;
        }
        
        $this->collection[] = $helper;
        
        return $helper;
    }
}

?>
