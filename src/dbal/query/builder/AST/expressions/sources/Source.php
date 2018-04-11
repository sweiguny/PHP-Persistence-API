<?php

namespace PPA\dbal\query\builder\AST\expressions\sources;

use PPA\dbal\query\builder\AST\expressions\Expression;

abstract class Source extends Expression
{
    
    
    
    private $alias;
    
    public function __construct(string $alias = null)
    {
        $this->alias = $alias;
    }

    public function getAlias()
    {
        return $this->alias;
    }

}

?>
