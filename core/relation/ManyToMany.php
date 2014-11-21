<?php

namespace PPA\core\relation;

use PPA\core\EntityProperty;

class ManyToMany extends Relation {

    private $joinTable;
    private $column;
    private $x_column;
    
    public function __construct(EntityProperty $property, $fetch, $cascade, $mappedBy, $joinTable, $column, $x_column) {
        parent::__construct($property, $fetch, $cascade, $mappedBy);
        
        $this->joinTable = trim($joinTable);
        $this->column    = trim($column);
        $this->x_column  = trim($x_column);
    }
    
    public function getJoinTable() {
        return $this->joinTable;
    }

    public function getColumn() {
        return $this->column;
    }

    public function getX_column() {
        return $this->x_column;
    }

}

?>
