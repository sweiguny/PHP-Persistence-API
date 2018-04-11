<?php

namespace PPA\dbal\query\builder\AST\expressions\sources;

class Field extends Source
{
    
    private $fieldName;
    
    public function __construct(string $fieldName, string $alias = null)
    {
        parent::__construct($alias);
        
        $this->fieldName = $fieldName;
    }

    protected function stringifyAlias(): string
    {
        return $this->getAlias() == null ? "" : " AS '{$this->getAlias()}'";
    }
    
    public function toString(): string
    {
        return "`{$this->fieldName}`" . $this->stringifyAlias();
    }

}

?>
