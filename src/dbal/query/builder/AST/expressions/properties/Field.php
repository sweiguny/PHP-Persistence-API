<?php

namespace PPA\dbal\query\builder\AST\expressions\properties;

class Field extends Property
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
    
    public function __construct(string $name, string $tableReference = null, string $alias = null)
    {
        parent::__construct($alias);
        
        $this->name           = $name;
        $this->tableReference = $tableReference;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTableReference(): ?string
    {
        return $this->tableReference;
    }
    
    public function toString(): string
    {
        return ($this->tableReference == null ? "" : $this->tableReference . ".") . "`{$this->name}`" . $this->stringifyAlias();
    }

}

?>
