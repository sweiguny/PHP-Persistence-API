<?php

namespace PPA\core;

class EntityMetaDataMap {

    private static $instance;

    /**
     * @return EntityMetaDataMap
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
    
    /**
     * @param string $fullyQualifiedClassname
     * @return string The name of the table corresponding to the class.
     */
    public function getTableName($fullyQualifiedClassname) {
        $this->prepare($fullyQualifiedClassname);
        
        return $this->data[$fullyQualifiedClassname]["table"];
    }
    
    /**
     * @param type $classname
     * @return EntityProperty
     */
    public function getPrimaryProperty($fullyQualifiedClassname) {
        $this->prepare($fullyQualifiedClassname);
        
        return $this->data[$fullyQualifiedClassname]["primary"];
    }

    /**
     * @param string $fullyQualifiedClassname
     * @return array Array with all properties - indices are the property names.
     */
    public function getPropertiesByName($fullyQualifiedClassname) {
        $this->prepare($fullyQualifiedClassname);
        
        return $this->data[$fullyQualifiedClassname]["byName"];
    }
    
    /**
     * @param string $fullyQualifiedClassname
     * @return array Array with all properties - indices are the column names.
     */
    public function getPropertiesByColumn($fullyQualifiedClassname) {
        $this->prepare($fullyQualifiedClassname);
        
        return $this->data[$fullyQualifiedClassname]["byColumn"];
    }
    
    /**
     * @param string $fullyQualifiedClassname
     * @return array All relations of the class
     */
    public function getRelations($fullyQualifiedClassname) {
        $this->prepare($fullyQualifiedClassname);
        
        return $this->data[$fullyQualifiedClassname]["relations"];
    }
    
    /**
     * Gets all data from the analyzer and pushes it to the map.
     * 
     * @param string $fullyQualifiedClassname
     */
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
