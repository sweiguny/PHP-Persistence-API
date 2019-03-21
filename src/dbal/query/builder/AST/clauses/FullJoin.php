<?php

namespace PPA\dbal\query\builder\AST\clauses;

class FullJoin extends SQLClause
{
    public function toString(): string
    {
        return "FULL JOIN";
    }

}

?>
