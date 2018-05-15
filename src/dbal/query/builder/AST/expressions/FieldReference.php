<?php

namespace PPA\dbal\query\builder\AST\expressions;

/**
 * Description of FieldReference
 *
 * @author siwe
 */
class FieldReference extends Expression
{
    private $fieldName;
    private $indicator;

    public function __construct(string $fieldName, string $tableOrAliasIndicator = null)
    {
        $this->fieldName = $fieldName;
        $this->indicator = $tableOrAliasIndicator;
    }
    
    
    public function toString(): string
    {
        return ($this->indicator == null ? "" : "{$this->indicator}.") . "`{$this->fieldName}`";
    }

}

?>
