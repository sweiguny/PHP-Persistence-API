<?php

namespace PPA;

class EntityMetaDataMap {

    private static $instance;

    /**
     * @return EntityMap
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Holds the whole entity metadata.
     * 
     * @var array
     */
    private $data;

    private function __construct() {
        $this->data = array();
    }

    public function getTableName($classname) {
        $this->prepare($classname);
        
        return $this->data[$classname]["table"];
    }
    
    /**
     * 
     * @param type $classname
     * @return PersistenceProperty
     */
    public function getPrimaryProperty($classname) {
        $this->prepare($classname);
        
        return $this->data[$classname]["primary"];
    }


    public function getPropertiesByName($classname) {
        $this->prepare($classname);
        
        return $this->data[$classname]["byName"];
    }

    public function getPropertiesByColumn($classname) {
        $this->prepare($classname);
        
        return $this->data[$classname]["byColumn"];
    }

    private function prepare($classname) {
        if (!isset($this->data[$classname])) {
            $analyzer = new EntityAnalyzer($classname);
            
            # TODO: avoid double call of $analyzer->getPersistenceProperties
            $this->data[$classname]["byName"]   = $analyzer->getPersistencePropertiesByName();
            $this->data[$classname]["byColumn"] = $analyzer->getPersistencePropertiesByColumn();
            
            $this->data[$classname]["primary"]  = $analyzer->getPrimaryPersistenceProperty();
            $this->data[$classname]["table"]    = $analyzer->getPersistenceClassAttributes();
        }
    }
}

?>
