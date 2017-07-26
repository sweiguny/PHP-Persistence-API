<?php

namespace PPA\core\relation;

use PPA\core\EntityProperty;

class OneToOne extends Relation
{

    public function __construct(EntityProperty $property, $fetch, $cascade, $mappedBy)
    {
        parent::__construct($property, $fetch, $cascade, $mappedBy);
    }

}

?>
