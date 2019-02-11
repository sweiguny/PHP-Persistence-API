<?php

namespace PPA\dbal\query\builder\AST\clauses;

class Delete extends SQLClause
{
    public function __construct()
    {
        parent::__construct();
    }

    public function toString(): string
    {
        return "DELETE";
    }

}

?>
