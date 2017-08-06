<?php

namespace PPA\orm\mapping;

use InvalidArgumentException;
use LogicException;

/**
 * Loads annotations of the entities.
 */
class AnnotationLoader
{
    
    const PPA_ANNOTATION_PATH = __DIR__ . DIRECTORY_SEPARATOR . "annotation";
    
    private static $includePaths = [
        self::PPA_ANNOTATION_PATH
    ];

    public static function addIncludePath(string $path)
    {
        if (is_dir($path))
        {
            throw new InvalidArgumentException(sprintf("Path '%s' is not a directory.", $path));
        }
        
        self::$includePaths[] = $path;
    }
    
    /**
     *
     * @var AnnotationFactory
     */
    private $factory;

    public function __construct()
    {
        $this->factory = new AnnotationFactory();
    }
    
    public function load(AnnotationBag $bag): array
    {
//        print_r($bag);die();
        $loadedAnnotations = [];
        
        foreach ($bag->getClassAnnotations() as $classname => $parameters)
        {
            if (!$this->annotationExists($classname))
            {
                throw new LogicException(sprintf("Annotation '@%s' could not be loaded. Maybe you need to add/remove a leading slash.", $classname));
            }
            
            $loadedAnnotations[] = $this->factory->instantiate($bag->getOwner(), $classname, $parameters);
        }
        
        foreach ($bag->getPropertyAnnotations() as $propertyName => $annotations)
        {
            foreach ($annotations as $classname => $parameters)
            {
                if (!$this->annotationExists($classname))
                {
                    throw new LogicException(sprintf("Annotation '@%s' could not be loaded. Maybe you need to add/remove a leading slash.", $classname));
                }

                $loadedAnnotations[] = $this->factory->instantiate($bag->getOwner(), $classname, $parameters, $propertyName);
            }
        }
        
        return $loadedAnnotations;
    }
    
    private function annotationExists(string $classname)
    {
        if (class_exists($classname, false))
        {
            return true;
        }
        
        if ($this->hasNamespace($classname))
        {
//            echo "**********{$classname}**************";
//            spl_autoload_call($classname);
            
//            print_r(class_exists($classname));

            return class_exists($classname/*, false*/);
        }
        else
        {
            foreach (self::$includePaths as $path)
            {
                $file = $path . DIRECTORY_SEPARATOR . $classname . ".php";

                if (is_file($file))
                {
                    require_once $file;
                    return true;
                }
            }

            return false;
        }
    }
    
    private function hasNamespace(string $classname): bool
    {
        $splitted = explode("\\", $classname);

        return count($splitted) != 1;
    }
    
    private function functionName($param)
    {
        
    }
    
}

?>
