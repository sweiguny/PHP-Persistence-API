<?php

namespace PPA\orm\mapping;

/**
 * Loads annotations of the entities.
 */
class AnnotationLoader
{
    
    const PPA_ANNOTATION_PATH = __DIR__ . DIRECTORY_SEPARATOR . "annotation";
    
    private static $includePaths = [
        self::PPA_ANNOTATION_PATH
    ];


    public function __construct()
    {
        $reflector = new \ReflectionClass($this);
//        $reflector->get
    }
    
    public function load(AnnotationBag $bag)
    {
        print_r($bag->getClassAnnotations());
        
        foreach ($bag->getClassAnnotations() as $classname => $parameters)
        {
            $reflectionClass = new \ReflectionClass($classname);
            print_r($reflectionClass->getNamespaceName());
            print_r($classname);
            
            if (class_exists($classname, false))
            {
                
                require_once '';
            }
        }
    }
    
}

?>
