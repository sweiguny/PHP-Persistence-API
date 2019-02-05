<?php

namespace PPA\dbal\query\builder\AST\catalogObjects;

class _Field extends CatalogObject
{
    /**
     *
     * @var string
     */
    private $name;
    
    /**
     *
     * @var string
     */
    private $tableReference;
    
    public function __construct(string $name, string $tableReference = null)
    {
        parent::__construct(true);
        
        $this->name           = $name;
        $this->tableReference = $tableReference == null ? "" : $tableReference . ".";
    }

    public function getName(): string
    {
        return $this->name;
    }
    
    public function toString(): string
    {
        return $this->tableReference . $this->getDriver()->getOpenIdentifier() . $this->name . $this->getDriver()->getCloseIdentifier();
    }

}

?>
