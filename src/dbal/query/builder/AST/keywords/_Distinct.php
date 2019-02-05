<?php

namespace PPA\dbal\query\builder\AST\keywords;

class _Distinct extends Keyword
{
    public function __construct()
    {
        parent::__construct(false);
    }
    
    public function toString(): string
    {
        return "DISTINCT";
    }

}

?>
