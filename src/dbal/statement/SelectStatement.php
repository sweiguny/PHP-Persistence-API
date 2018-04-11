<?php

namespace PPA\dbal\statement;

use PPA\dbal\query\builder\AST\expressions\sources\Source;
use PPA\dbal\query\builder\AST\SelectList;


class SelectStatement extends Source
{
    
    private $selectList;
    
    private $fromTable;
    
    /**
     * 
     * @param string $fromTable
     * @param SelectList $selectList
     * @param string $alias Used, if statement is a subquery.
     */
    public function __construct(string $fromTable, SelectList $selectList, string $alias = null)
    {
        parent::__construct($alias);
        
        $this->fromTable  = $fromTable;
        $this->selectList = $selectList;
    }

    public function createSelectList(Source ...$list)
    {
        return new SelectList($list);
    }

    public function toString(): string
    {
        throw new Exception("Not yet implemented...");
    }

}

?>
