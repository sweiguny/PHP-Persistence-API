<?php

namespace PPA\dbal\query\builder\AST\clauses;

class On extends SQLClause
{
    public function toString(): string
    {
        return "ON";
    }

}

?>
