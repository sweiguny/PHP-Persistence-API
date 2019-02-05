<?php

namespace PPA\dbal\query\builder\AST\clauses;

class Join extends SQLClause
{
    public function toString(): string
    {
        return "JOIN";
    }

}

?>
