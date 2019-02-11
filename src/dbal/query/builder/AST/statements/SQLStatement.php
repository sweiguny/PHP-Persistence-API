<?php

namespace PPA\dbal\query\builder\AST\statements;

use PPA\dbal\drivers\DriverInterface;
use PPA\dbal\query\builder\AST\ASTCollection;
use PPA\dbal\query\builder\AST\ASTNode;

abstract class SQLStatement extends ASTNode
{
    /**
     *
     * @var ASTCollection
     */
    protected $collection;
    
    public function __construct(DriverInterface $driver)
    {
        parent::__construct(true);
        
        $this->injectDriver($driver);
        
        $this->collection = new ASTCollection($driver);
    }

    public function toString(): string
    {
        return $this->collection->toString();
    }

}

?>
