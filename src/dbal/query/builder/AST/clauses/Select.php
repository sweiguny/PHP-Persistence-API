<?php

namespace PPA\dbal\query\builder\AST\clauses;

class Select extends SQLClause
{
    public function __construct()
    {
        parent::__construct();
    }

    public function toString(): string
    {
        return "SELECT";
    }

}

?>
