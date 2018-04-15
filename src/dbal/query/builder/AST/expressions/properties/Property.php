<?php

namespace PPA\dbal\query\builder\AST\expressions\properties;

use PPA\dbal\query\builder\AST\expressions\Alias;
use PPA\dbal\query\builder\AST\expressions\Expression;

/**
 * Description of Property
 *
 * @author siwe
 */
abstract class Property extends Expression
{
    use Alias;
    
    public function __construct(string $alias = null)
    {
        $this->alias = $alias;
    }
}

?>
