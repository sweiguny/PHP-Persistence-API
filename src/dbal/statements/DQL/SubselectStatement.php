<?php

namespace PPA\dbal\statements\DQL;

use PPA\dbal\query\builder\AST\expressions\sources\Source;

/**
 * Description of SubselectStatement
 *
 * @author siwe
 */
class SubselectStatement extends SelectStatement
{
    public function __construct(string $alias, Source ...$sources)
    {
        parent::__construct($sources);
        $this->setAlias($alias);
    }
    
    public function toString(): string
    {
        return parent::toString() . " AS {$this->getAlias()}";
    }
}

?>
