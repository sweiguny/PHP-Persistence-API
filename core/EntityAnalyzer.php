<?php

namespace PPA\core;

use InvalidArgumentException;
use PPA\core\exception\AnnotationException;
use PPA\core\relation\ManyToMany;
use PPA\core\relation\OneToMany;
use PPA\core\relation\OneToOne;
use ReflectionClass;


class EntityAnalyzer {
    
    /**
     * @var string The classname to be analyzed.
     */
    private $classname;
    
    /**
     * The reflector.
     * 
     * @var ReflectionClass 
     */
    private $reflector;
    
    private $primaryProperty;
    private $tableName;
    private $propertiesByName;
    private $propertiesByColumn;
    private $relations;
    
    private $analyzed = false;


    /**
     * All possible annotations and their available parameters.
     * 
     * @var array
     */
    private $propertyAnnotations = array(
        "@id"         => array(),
        "@column"     => array("name"),
        "@table"      => array("name"),
        "@oneToOne"   => array("mappedBy", "fetch"),
        "@oneToMany"  => array("mappedBy", "fetch"),
        "@manyToMany" => array("mappedBy", "fetch"),
        "@joinTable"  => array("name", "column", "x_column")
    );

    /**
     * @param string $className The classname to be analyzed.
     * @throws InvalidArgumentException If class is not a subclass of \PPA\Entity.
     */
    public function __construct($fullyQualifiedClassname) {
        $this->reflector = new ReflectionClass($fullyQualifiedClassname);
        
        if (!$this->reflector->isSubclassOf("\\PPA\\core\\Entity")) {
            throw new InvalidArgumentException("Class '{$fullyQualifiedClassname}' must be a subclass of \\PPA\\core\\Entity.");
        }
        
        $this->classname = $fullyQualifiedClassname;
    }
    
    public function doAnalysis() {
        if ($this->analyzed) {
            # TODO: trigger error and/or log message
        } else {
            
            $annotations = $this->extractAnnotations($this->reflector->getDocComment());
            
            if (isset($annotations["@table"]) && isset($annotations["@table"]["name"])) {
                $this->tableName = $annotations["@table"]["name"];
            } else {
                $this->tableName = strtolower($this->reflector->getShortName());
            }
            
            
            # --------------------
            
            
            $primaryProperty    = null;
            $propertiesByName   = array();
            $propertiesByColumn = array();
            $relations          = array();
            $properties         = $this->reflector->getProperties();
            
            foreach ($properties as $property) {
                $annotations = $this->extractAnnotations($property->getDocComment());
                
                $pprop = new PersistenceProperty($this->classname, $property->getName());
                $pprop->setAccessible(true);

                if (isset($annotations["@column"])) {
                    
                    if (isset($annotations["@id"])) {
                        $pprop->makePrimary();
                        $primaryProperty = $pprop;
                    }

                    if (isset($annotations["@column"]["name"])) {
                        $pprop->setColumn($annotations["@column"]["name"]);
                    } else {
                        $pprop->setColumn($property->getName());
                    }

                    if (isset($annotations["@oneToOne"])) {
                        $relation = new OneToOne($pprop, $annotations["@oneToOne"]["fetch"], $annotations["@oneToOne"]["mappedBy"]);
                        $relations[] = $relation;
                        
                        $pprop->setRelation($relation);
                    }
                
                    $propertiesByName[$pprop->getName()] = $pprop;
                    $propertiesByColumn[$pprop->getColumn()] = $pprop;

                } else if (isset($annotations["@oneToMany"]) && isset($annotations["@joinTable"])) {
                    $relation = new OneToMany($pprop, $annotations["@oneToMany"]["fetch"], $annotations["@oneToMany"]["mappedBy"], $annotations["@joinTable"]["x_column"]);
                    $relations[] = $relation;
                    $pprop->setRelation($relation);

                    $propertiesByName[$pprop->getName()] = $pprop;
                } else if (isset($annotations["@manyToMany"]) && isset($annotations["@joinTable"])) {
                    $relation = new ManyToMany($pprop, $annotations["@manyToMany"]["fetch"], $annotations["@manyToMany"]["mappedBy"], $annotations["@joinTable"]["name"], $annotations["@joinTable"]["column"], $annotations["@joinTable"]["x_column"]);
                    $relations[] = $relation;
                    $pprop->setRelation($relation);

                    $propertiesByName[$pprop->getName()] = $pprop;
                } else {
                    $this->handleUncombinables($annotations);
                }
            }
            
            if ($primaryProperty == null) {
                throw new AnnotationException("Entity '{$this->classname}' does not have an @id annotation.");
            }
            $this->primaryProperty = $primaryProperty;
            $this->propertiesByName = $propertiesByName;
            $this->propertiesByColumn = $propertiesByColumn;
            $this->relations = $relations;
            
            $this->analyzed = true;
        }
    }
    
    private function handleUncombinables(array $annotations) {
        if (isset($annotations["@joinTable"])) {
            if (!isset($annotations["@manyToMany"])) {
                $message = "Entity '{$this->classname}' provides an @joinTable annotation, but not an @manyToMany.";
            } else if (!isset($annotations["@oneToMany"])) {
                $message = "Entity '{$this->classname}' provides an @joinTable annotation, but not an @oneToMany.";
            }
        } else if (isset($annotations["@manyToMany"]) && !isset($annotations["@joinTable"])) {
            $message = "Entity '{$this->classname}' provides an @manyToMany annotation, but not an @joinTable.";
        } else if (isset($annotations["@oneToMany"]) && !isset($annotations["@joinTable"])) {
            $message = "Entity '{$this->classname}' provides an @oneToMany annotation, but not an @joinTable.";
        }
        
        throw new AnnotationException($message);
    }

    public function getPrimaryProperty() {
        return $this->primaryProperty;
    }

    public function getTableName() {
        return $this->tableName;
    }

    public function getPropertiesByName() {
        return $this->propertiesByName;
    }

    public function getPropertiesByColumn() {
        return $this->propertiesByColumn;
    }

    public function getRelations() {
        return $this->relations;
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
