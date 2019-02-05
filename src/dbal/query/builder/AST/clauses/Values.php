<?php

namespace PPA\dbal\query\builder\AST\clauses;

class Values extends SQLClause
{
    
    public function toString(): string
    {
        return "VALUES";
    }

}

?>
