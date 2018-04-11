<?php

namespace PPA\dbal\query\builder\AST\expressions\sources;

class AsteriskWildcard extends Source
{
    
    public function toString(): string
    {
        return "*";
    }

}

?>
