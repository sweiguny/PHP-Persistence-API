<?php

namespace PPA\core\relation;

use PPA\core\EntityProperty;

class OneToMany extends Relation {

    private $x_column;

    public function __construct(EntityProperty $property, $fetch, $cascade, $mappedBy, $x_column) {
        parent::__construct($property, $fetch, $cascade, $mappedBy);
        
        $this->x_column = trim($x_column);
    }

    /**
     * 
     * @return string
     */
    public function getX_column() {
        return $this->x_column;
    }

}

?>
