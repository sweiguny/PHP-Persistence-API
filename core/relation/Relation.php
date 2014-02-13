<?php

namespace PPA\core\relation;

use PPA\core\exception\FetchException;
use PPA\core\PersistenceProperty;

abstract class Relation {

    private $property;
    private $fetch;
    private $mappedBy;

    public function __construct(PersistenceProperty $property, $fetch, $mappedBy) {
        $this->fetch = trim($fetch);
        
        if (!in_array($this->fetch, array("lazy", "eager"))) {
            throw new FetchException("Fetch-type can only be 'lazy' or 'eager'.");
        }

        $this->property = $property;
        $this->mappedBy = str_replace("_", "\\", $mappedBy);
    }

    public function getProperty() {
        return $this->property;
    }

    public function getMappedBy() {
        return $this->mappedBy;
    }

    public function isLazy() {
        return $this->fetch == "lazy";
    }

    public function isEager() {
        return $this->fetch == "eager";
    }

}

?>
