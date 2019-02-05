<?php

namespace PPA\dbal\query\builder\AST\clauses;

class Into extends SQLClause
{
    
    public function toString(): string
    {
        return "INTO";
    }

}

?>
