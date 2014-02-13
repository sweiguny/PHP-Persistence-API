<?php

namespace PPA\core\relation;

use PPA\core\PersistenceProperty;

class OneToOne extends Relation {

    public function __construct(PersistenceProperty $property, $fetch, $mappedBy) {
        parent::__construct($property, $fetch, $mappedBy);
    }

}

?>
