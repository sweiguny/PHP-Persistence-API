<?php

namespace PPA\core;

use InvalidArgumentException;
use PPA\core\exception\FetchException;

class Relation {

    private $property;
    private $type;
    private $fetch;
    private $mappedBy;
    private $joinTable;
    
    public function __construct(PersistenceProperty $property, $type, $fetch, $mappedBy, array $joinTable = null) {
        if (!in_array($fetch, array("lazy", "eager"))) {
            throw new FetchException("Fetch-type can only be 'lazy' or 'eager'.");
        }
        
        $this->property = $property;
        $this->type     = $type;
        $this->fetch    = $fetch;
        $this->mappedBy = str_replace("_", "\\", $mappedBy);
        
        if ($this->isOneToMany() || $this->isManyToMany()) {
            if ($joinTable == null) {
                throw new InvalidArgumentException('If relation is @manyToMany, \'$joinTable\' must be set.');
            } else {
                $this->joinTable = $joinTable;
            }
        }
    }

    public function getProperty() {
        return $this->property;
    }

    public function getMappedBy() {
        return $this->mappedBy;
    }
    
    public function getJoinTable() {
        return $this->joinTable;
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
    
    public function isOneToMany() {
        return $this->type == "oneToMany";
    }
    
    public function isManyToMany() {
        return $this->type == "manyToMany";
    }
}

?>
