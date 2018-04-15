<?php

namespace PPA\dbal\query\builder\AST\expressions\sources;

use PPA\dbal\query\builder\AST\expressions\Alias;
use PPA\dbal\query\builder\AST\expressions\Expression;

abstract class Source extends Expression
{
    use Alias;
    
    public function __construct(string $alias = null)
    {
        $this->alias = $alias;
    }

}

?>