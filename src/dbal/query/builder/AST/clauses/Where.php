<?php

namespace PPA\dbal\query\builder\AST\clauses;

class Where extends SQLClause
{
    
    public function toString(): string
    {
        return "WHERE";
    }

}

?>
