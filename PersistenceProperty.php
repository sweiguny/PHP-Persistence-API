<?php

namespace PPA;

use ReflectionProperty;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class PersistenceProperty extends ReflectionProperty {

    protected $column;
    protected $relation;
    protected $isId;

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
    public function isId() {
        return $this->isId;
    }

    public function setAsId($isId = true) {
        $this->isId = (bool)$isId;
    }


}

?>
