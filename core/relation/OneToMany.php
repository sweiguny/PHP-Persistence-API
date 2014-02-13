<?php

namespace PPA\core\relation;

use PPA\core\PersistenceProperty;

class OneToMany extends Relation {

    private $x_column;

    public function __construct(PersistenceProperty $property, $fetch, $mappedBy, $x_column) {
        parent::__construct($property, $fetch, $mappedBy);
        
        $this->x_column = trim($x_column);
    }

    public function getX_column() {
        return $this->x_column;
    }

}

?>
