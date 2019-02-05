<?php

namespace PPA\dbal\query\builder\AST\clauses;

class Having extends SQLClause
{
    
    public function toString(): string
    {
        return "HAVING";
    }

}

?>
