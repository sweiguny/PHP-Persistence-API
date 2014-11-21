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
    
    public function getClass() {
        return $this->class;
    }

    public function getColumn() {
        return $this->column;
    }
    
    public function setColumn($column) {
        $this->column = $column;
    }
    
    /**
     * @param Relation $relation
     */
    public function setRelation(Relation $relation) {
        $this->relation = $relation;
    }
    
    /**
     * @return Relation
     */
    public function getRelation() {
        return $this->relation;
    }

    /**
     * Checks if the property represents a relation to another entity.
     * 
     * @return bool
     */
    public function hasRelation() {
        return isset($this->relation);
    }
    
    /**
     * Checks if the property is the primary.
     * 
     * @return bool
     */
    public function isPrimary() {
        return $this->isPrimary;
    }

    /**
     * Defines the property as primary.
     * 
     * @param bool $primary 
     */
    public function makePrimary($primary = true) {
        $this->isPrimary = (bool)$primary;
    }

}

?>
