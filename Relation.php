<?php

namespace PPA;

use DomainException;

class Relation {

    private $type;
    private $fetch;
    private $mappedBy;
    
    public function __construct($type, $fetch, $mappedBy) {
        if (!in_array($fetch, array("lazy", "eager"))) {
            throw new DomainException("Fetch-type can only be 'lazy' or 'eager'.");
        }
        
        $this->type     = $type;
        $this->fetch    = $fetch;
        $this->mappedBy = str_replace("_", "\\", $mappedBy);
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
    
    public function isOneToOne() {
        return $this->type == "oneToOne";
    }
}

?>
