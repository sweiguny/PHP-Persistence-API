<?php

namespace PPA\dbal\query\builder\AST\catalogObjects;

use PPA\dbal\query\builder\AST\SQLDataSource;

class _Table extends CatalogObject implements SQLDataSource
{
    /**
     *
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        parent::__construct(true);
        
        $this->name = $name;
    }
    
    public function toString(): string
    {
        return $this->getDriver()->getOpenIdentifier() . $this->name . $this->getDriver()->getCloseIdentifier();
    }
    
}

?>
