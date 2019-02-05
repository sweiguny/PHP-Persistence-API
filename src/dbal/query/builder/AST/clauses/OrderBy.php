<?php

namespace PPA\dbal\query\builder\AST\clauses;

class OrderBy extends SQLClause
{
    
    public function toString(): string
    {
        return "ORDER BY";
    }

}

?>
