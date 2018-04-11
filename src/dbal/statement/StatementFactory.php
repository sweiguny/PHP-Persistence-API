<?php

namespace PPA\dbal\statement;

class StatementFactory
{
    
    public function createSelectStatement($fromTable, $selectList)
    {
        return new SelectStatement($fromTable, $selectList);
    }
    
}

?>
