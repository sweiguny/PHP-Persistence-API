<?php

namespace PPA\dbal\query\builder\AST\clauses;

class CrossJoin extends SQLClause
{
    public function toString(): string
    {
        return "CROSS JOIN";
    }

}

?>
