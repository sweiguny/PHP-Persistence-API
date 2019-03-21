<?php

namespace PPA\dbal\query\builder\AST\clauses;

class LeftJoin extends SQLClause
{
    public function toString(): string
    {
        return "LEFT JOIN";
    }

}

?>
