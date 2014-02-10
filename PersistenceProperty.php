<?php

namespace PPA;

use ReflectionProperty;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class PersistenceProperty extends ReflectionProperty {

    protected $column;

    public function __construct($class, $name) {
        parent::__construct($class, $name);
    }

    public function getColumn() {
        return $this->column;
    }
    
    public function setColumn($column) {
        $this->column = $column;
    }

}

?>
