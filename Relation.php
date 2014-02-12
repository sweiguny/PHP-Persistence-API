<?php

namespace PPA;

use InvalidArgumentException;
use PPA\exception\FetchException;

class Relation {

    private $type;
    private $fetch;
    private $mappedBy;
    private $joinTable;
    
    public function __construct($type, $fetch, $mappedBy, array $joinTable = null) {
        if (!in_array($fetch, array("lazy", "eager"))) {
            throw new FetchException("Fetch-type can only be 'lazy' or 'eager'.");
        }
        
        $this->type     = $type;
        $this->fetch    = $fetch;
        $this->mappedBy = str_replace("_", "\\", $mappedBy);
        
        if ($this->isManyToMany()) {
            if ($joinTable == null) {
                throw new InvalidArgumentException('If relation is @manyToMany, \'$joinTable\' must be set.');
            } else {
                $this->joinTable = $joinTable;
            }
        }
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
    
    public function isManyToMany() {
        return $this->type == "manyToMany";
    }
}

?>
