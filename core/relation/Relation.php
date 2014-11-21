<?php

namespace PPA\core\relation;

use InvalidArgumentException;
use PPA\core\EntityProperty;
use PPA\core\exception\FetchException;
use PPA\PPA;

abstract class Relation {

    /**
     *
     * @var EntityProperty
     */
    private $property;
    
    /**
     *
     * @var string
     */
    private $fetch;
    
    /**
     *
     * @var string
     */
    private $cascade;
    
    /**
     *
     * @var string
     */
    private $mappedBy;

    public function __construct(EntityProperty $property, $fetch, $cascade, $mappedBy) {
        $this->fetch   = trim($fetch);
        $this->cascade = trim($cascade);
        
        if (!in_array($this->fetch, array("lazy", "eager"))) {
            throw new FetchException("Fetch-type can only be 'lazy' or 'eager'.");
        }
        
//        \PPA\prettyDump($property);
        if (!in_array($this->cascade, PPA::$LEGAL_CASCADING_TYPES)) {
            throw new InvalidArgumentException("The cascade type for property '{$property->getName()}' of class '\\{$property->getClass()}' was set to '{$this->cascade}'. But the only legal values are '" . implode("', '", PPA::$LEGAL_CASCADING_TYPES) . "'.");
        }
        
        $this->property = $property;
        $this->mappedBy = str_replace("_", "\\", $mappedBy);
    }

    /**
     * 
     * @return EntityProperty
     */
    public function getProperty() {
        return $this->property;
    }

    /**
     * 
     * @return string
     */
    public function getMappedBy() {
        return $this->mappedBy;
    }

    /**
     * 
     * @return boolean
     */
    public function isLazy() {
        return $this->fetch == "lazy";
    }

    /**
     * 
     * @return boolean
     */
    public function isEager() {
        return $this->fetch == "eager";
    }

    /**
     * 
     * @return boolean
     */
    public function isCascadeTypePersist() {
        return $this->cascade == "all" || $this->cascade == "persist";
    }

    /**
     * 
     * @return boolean
     */
    public function isCascadeTypeRemove() {
        return $this->cascade == "all" || $this->cascade == "remove";
    }
}

?>
