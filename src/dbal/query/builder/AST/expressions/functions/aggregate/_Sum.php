<?php

namespace PPA\dbal\query\builder\AST\expressions\functions\aggregate;

class _Sum extends AggregateFn
{
    public function __construct()
    {
        parent::__construct();
        
        
        echo "<pre>";
        print_r("in class ".__CLASS__);
        echo "</pre>";
    }
}

?>
