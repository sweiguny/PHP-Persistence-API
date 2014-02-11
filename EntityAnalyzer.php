<?php

namespace PPA;

use InvalidArgumentException;
use ReflectionClass;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class EntityAnalyzer {

    /**
     * @param string $classname
     * @return boolean True, if class is a subclass of \PPA\Entity, false, if not.
     */
    public static function isEntity($classname) {
        $reflector = new ReflectionClass($classname);
        return $reflector->isSubclassOf("\\PPA\\Entity");
    }
    
    /**
     * @var string The classname to be analyzed.
     */
    private $className;
    
    /**
     * The reflector.
     * 
     * @var ReflectionClass 
     */
    private $reflector;
    
    private $primaryProperty;
    
    /**
     * All possible annotations and their available parameters.
     * 
     * @var array
     */
    private $propertyAnnotations = array(
        "@id"       => array(),
        "@column"   => array("name"),
        "@table"    => array("name"),
        "@oneToOne" => array("mappedBy", "fetch")
    );

    /**
     * @param string $className The classname to be analyzed.
     * @throws InvalidArgumentException If class is not a subclass of \PPA\Entity.
     */
    public function __construct($className) {
        $this->reflector = new ReflectionClass($className);
        
        if (!$this->reflector->isSubclassOf("\\PPA\\Entity")) {
            throw new InvalidArgumentException("Class '{$className}' must be a subclass of \\PPA\\Entity.");
        }
        
        $this->className = $className;
    }
    
    public function getPersistenceClassAttributes() {
        $annotations = $this->extractAnnotations($this->reflector->getDocComment());
//        prettyDump($annotations);
        if (isset($annotations["@table"]) && isset($annotations["@table"]["name"])) {
            return $annotations["@table"]["name"];
        } else {
            return strtolower($this->reflector->getShortName());
        }
    }

    public function getPersistencePropertiesByName() {
        return $this->getPersistenceProperties("byName");
    }
    
    public function getPersistencePropertiesByColumn() {
        return $this->getPersistenceProperties("byColumn");
    }
    
    /**
     * Returns a list of properties of an entity.
     * 
     * @return array A List of \PPA\PersistenceProperty.
     */
    private function getPersistenceProperties($by) {
        $reflectionProperties  = $this->reflector->getProperties();
        $persistenceProperties = array();
        
        foreach ($reflectionProperties as $reflectionProperty) {
            
            $annotations = $this->extractAnnotations($reflectionProperty->getDocComment());
//            prettyDump($annotations);
            
            if (isset($annotations["@column"])) {
                $pprop = new PersistenceProperty($this->className, $reflectionProperty->getName());
                $pprop->setAccessible(true);
                
                if (isset($annotations["@id"])) {
                    $pprop->setAsId();
                    $this->primaryProperty = $pprop;
                }
                
                if (isset($annotations["@column"]["name"])) {
                    $pprop->setColumn($annotations["@column"]["name"]);
                } else {
                    $pprop->setColumn($reflectionProperty->getName());
                }
                
                if (isset($annotations["@oneToOne"])) {
                    $pprop->setRelation(new Relation("oneToOne", $annotations["@oneToOne"]["fetch"], $annotations["@oneToOne"]["mappedBy"]));
                }
                
                if ($by == "byName") {
                    $persistenceProperties[$pprop->getName()] = $pprop;
                } else {
                    $persistenceProperties[$pprop->getColumn()] = $pprop;
                }
            }
        }
        
        return $persistenceProperties;
    }
    
    /**
     * @return \PPA\PersistenceProperty
     */
    public function getPrimaryPersistenceProperty() {
        return $this->primaryProperty;
    }


    /**
     * @param string $docComment The documentation of a property.
     * @return array The extracted Annotations.
     */
    private function extractAnnotations($docComment) {
        
        // The extracted annotations to be returned.
        $extracted = array();
        $divided   = $this->divideAnnotations($docComment);
        
        foreach ($divided as $annotation) {
            
            // Process every possible annotation.
            foreach ($this->propertyAnnotations as $propertyAnnoKey => $propertyAnnoValue) {
                
                // Pattern for getting lines, which contains the $propertyParamKey
                // and to capture everything within the parenthesis.
                $pattern = "#{$propertyAnnoKey}[\s]*\(?(.+)\)?#i";
                $matches = array();

                // If key exists, continue extraction.
                if (preg_match($pattern, $annotation, $matches)) {
                    
                    // The params to be prepared.
                    $extracted[$propertyAnnoKey] = array();
                    
                    // Split the parameter list.
                    $parameters = explode(",", $matches[1]);
                    
                    foreach ($parameters as $parameter) {
                        
                        // Pattern to extract the param-key and param-value.
                        $pattern = "#[\s]*([\w]+)[\s]*=[\s]*[\"\']([\w]+)[\"\']#";
                        
                        if (preg_match($pattern, $parameter, $matches)) {
                            
                            // If param key is not in white-list, an error is triggered.
                            // This requires a case-insensitive search.
                            $key = array_search(strtolower($matches[1]), array_map('strtolower', $propertyAnnoValue));
                            if ($key !== false) {
                                if ($propertyAnnoValue[$key] != "mappedBy") {
                                    $matches[2] = strtolower($matches[2]);
                                }
                                $extracted[$propertyAnnoKey][$propertyAnnoValue[$key]] = $matches[2];
                            } else {
                                trigger_error("Parameter '{$matches[1]}' of Annotation '{$propertyAnnoKey}' is not provided.", E_USER_NOTICE);
                            }
                        }
                    }
                }
            }
        }
        
        return $extracted;
    }
    
    /**
     * Trims and splits the documentation.
     * 
     * @param string $docComment The documentation of the property.
     * @return array
     */
    private function divideAnnotations($docComment) {
        return explode("*", substr($docComment, 3, -2));
    }
}

?>
