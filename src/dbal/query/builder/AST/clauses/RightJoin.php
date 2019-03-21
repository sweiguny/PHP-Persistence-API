<?php

namespace PPA\dbal\query\builder\AST\clauses;

class RightJoin extends SQLClause
{
    public function toString(): string
    {
        return "RIGHT JOIN";
    }

}

?>
