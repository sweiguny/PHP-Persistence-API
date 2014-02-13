<?php

namespace PPA\core;

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

    public function getTableName($fullyQualifiedClassname) {
        $this->prepare($fullyQualifiedClassname);
        
        return $this->data[$fullyQualifiedClassname]["table"];
    }
    
    /**
     * 
     * @param type $classname
     * @return EntityProperty
     */
    public function getPrimaryProperty($fullyQualifiedClassname) {
        $this->prepare($fullyQualifiedClassname);
        
        return $this->data[$fullyQualifiedClassname]["primary"];
    }


    public function getPropertiesByName($fullyQualifiedClassname) {
        $this->prepare($fullyQualifiedClassname);
        
        return $this->data[$fullyQualifiedClassname]["byName"];
    }

    public function getPropertiesByColumn($fullyQualifiedClassname) {
        $this->prepare($fullyQualifiedClassname);
        
        return $this->data[$fullyQualifiedClassname]["byColumn"];
    }

    public function getRelations($fullyQualifiedClassname) {
        $this->prepare($fullyQualifiedClassname);
        
        return $this->data[$fullyQualifiedClassname]["relations"];
    }

    private function prepare($fullyQualifiedClassname) {
        if (!isset($this->data[$fullyQualifiedClassname])) {
            $analyzer = new EntityAnalyzer($fullyQualifiedClassname);
            $analyzer->doAnalysis();
            
            $this->data[$fullyQualifiedClassname]["byName"]    = $analyzer->getPropertiesByName();
            $this->data[$fullyQualifiedClassname]["byColumn"]  = $analyzer->getPropertiesByColumn();
            $this->data[$fullyQualifiedClassname]["primary"]   = $analyzer->getPrimaryProperty();
            $this->data[$fullyQualifiedClassname]["table"]     = $analyzer->getTableName();
            $this->data[$fullyQualifiedClassname]["relations"] = $analyzer->getRelations();
        }
    }
}

?>
