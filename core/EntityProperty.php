<?php

namespace PPA\core;

use PPA\core\relation\Relation;
use ReflectionProperty;

class EntityProperty extends ReflectionProperty {

    protected $column;
    protected $relation;
    protected $isPrimary;

    public function __construct($class, $name) {
        parent::__construct($class, $name);
    }

    public function getColumn() {
        return $this->column;
    }
    
    public function setColumn($column) {
        $this->column = $column;
    }
    
    public function setRelation(Relation $relation) {
        $this->relation = $relation;
    }
    
    /**
     * 
     * @return Relation
     */
    public function getRelation() {
        return $this->relation;
    }

    /**
     * 
     * @return bool
     */
    public function hasRelation() {
        return isset($this->relation);
    }
    
    /**
     * 
     * @return bool
     */
    public function isPrimary() {
        return $this->isPrimary;
    }

    public function makePrimary($primary = true) {
        $this->isPrimary = (bool)$primary;
    }


}

?>
