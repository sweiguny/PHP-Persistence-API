<?php

namespace PPA\dbal\query\builder\AST\clauses\join;

use PPA\dbal\query\builder\AST\expressions\sources\Table;
use PPA\dbal\query\builder\AST\SQLElementInterface;

/**
 * Description of Join
 *
 * @author siwe
 */
class Join implements SQLElementInterface
{
    
    private $table;


    public function __construct(string $name, string $alias = null)
    {
        $this->table = new Table($name, $alias);
        
    }

    public function toString(): string
    {
        return "JOIN " . $this->table->toString();
    }

}

?>
