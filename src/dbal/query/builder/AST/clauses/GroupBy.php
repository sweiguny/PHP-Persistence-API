<?php

namespace PPA\dbal\query\builder\AST\clauses;

class GroupBy extends SQLClause
{
    
    public function toString(): string
    {
        return "GROUP BY";
    }

}

?>
