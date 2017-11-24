<?php

namespace PPA\orm;

class Analysis
{
    /**
     * @var string The classname to be analyzed.
     */
    private $classname;

    /**
     * The property that represents the primary key in the table.
     * 
     * @var EntityProperty
     */
    private $primaryProperty;

    /**
     * The name of the table to which the entity is mapped.
     * 
     * @var string
     */
    private $tableName;

    /**
     * An array filled with properties indexed by the property name.
     * 
     * @var array
     */
    private $propertiesByName;

    /**
     * An array filled with properties indexed by the column name.
     * 
     * @var array
     */
    private $propertiesByColumn;

    /**
     * An array filled with all relations of the entity.
     * 
     * @var array
     */
//    private $relations;
    
    public function __construct(string $classname, EntityProperty $primaryProperty, $tableName, $propertiesByName, $propertiesByColumn)
    {
        $this->classname          = $classname;
        $this->primaryProperty    = $primaryProperty;
        $this->tableName          = $tableName;
        $this->propertiesByName   = $propertiesByName;
        $this->propertiesByColumn = $propertiesByColumn;
//        $this->relations          = $relations;
    }

    public function getClassname()
    {
        return $this->classname;
    }

    public function getPrimaryProperty(): EntityProperty
    {
        return $this->primaryProperty;
    }

    public function getTableName()
    {
        return $this->tableName;
    }

    public function getPropertiesByName()
    {
        return $this->propertiesByName;
    }

    public function getPropertiesByColumn()
    {
        return $this->propertiesByColumn;
    }
    
}

?>
