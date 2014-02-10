<?php

namespace PPA;

use ReflectionClass;
use ReflectionProperty;

/**
 * @copyright copyright (c) by Simon Weiguny <s.weiguny@gmail.com>
 * @author Simon Weiguny - 10.02.2014
 */
class Analyzer {

    private $className;
    private $reflector;
    
    private $propertyAnnotations = array(
        "@column" => array("name")
    );

    public function __construct($className) {
        $this->className = $className;
        $this->reflector = new ReflectionClass($this->className);
    }
    
    public function getPersistenceClassAttributes() {
        
    }

    public function getPersistenceProperties() {
        $reflectionProperties = $this->reflector->getProperties();
        
        $properties = array();
        
        foreach ($reflectionProperties as $reflectionProperty) {
//            prettyDump($reflectionProperty);
            $this->extractAnnotations($reflectionProperty->getDocComment());
        }
    }
    
    public function extractAnnotations($docComment) {
        
        /**
         * The extracted annotations to be returned.
         */
        $extracted = array();
        $divided   = $this->divideAnnotations($docComment);
        
        /**
         * Process each line.
         */
        foreach ($divided as $annotation) {
            
            /**
             * Process every possible annotation.
             */
            foreach ($this->propertyAnnotations as $propertyAnnoKey => $propertyAnnoValue) {
                
                /**
                 * Pattern for getting lines, which contains the $propertyParamKey
                 * and to capture everything within the parenthesis.
                 */
                $pattern = "#{$propertyAnnoKey}[\s]*\((.+)\)#i";

                /**
                 * If key exists, continue extraction.
                 */
                if (preg_match($pattern, $annotation, $matches)) {
                    
                    /**
                     * The params to be prepared.
                     */
                    $extractedParams = array();
                    
                    /**
                     * Split the parameter list.
                     */
                    $parameters = explode(",", $matches[1]);
                    
                    foreach ($parameters as $parameter) {
                        
                        /**
                         * Pattern to extract the param-key and param-value.
                         */
                        $pattern = "#[\s]*([\w]+)[\s]*=[\s]*[\"\']([\w]+)[\"\']#";
                        
                        if (preg_match($pattern, $parameter, $matches)) {
                            
                            /**
                             * If param key is not in white-list, an error is triggered.
                             */
                            if (in_array($matches[1], $propertyAnnoValue)) {
                                $extractedParams[$matches[1]] = $matches[2];
                            } else {
                                trigger_error("Parameter '{$matches[1]}' of Annotation '{$propertyAnnoKey}' is not provided.", E_USER_NOTICE);
                            }
                        }
                    }
                    
                    if (!empty($extractedParams)) {
                        $extracted[$propertyAnnoKey] = $extractedParams;
                    }
                }
            }

            prettyDump($extracted);
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
