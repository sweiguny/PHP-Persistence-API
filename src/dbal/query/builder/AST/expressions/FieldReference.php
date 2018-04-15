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
    private $tableOrAliasIndicator;

    public function __construct(string $fieldName, string $tableOrAliasIndicator = null)
    {
        $this->fieldName = $fieldName;
        $this->tableOrAliasIndicator = $tableOrAliasIndicator;
    }
    
    
    public function toString(): string
    {
        return ($this->tableOrAliasIndicator == null ? "" : "{$this->tableOrAliasIndicator}.") . "`{$this->fieldName}`";
    }

}

?>
