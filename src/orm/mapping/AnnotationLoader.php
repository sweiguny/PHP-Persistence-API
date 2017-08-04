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
    
    private $factory;

    public function __construct()
    {
        $this->factory = new AnnotationFactory();
    }
    
    public function load(AnnotationBag $bag)
    {
        echo PHP_EOL;
//        echo print_r($bag->getClassAnnotations(), true).PHP_EOL;
        
        foreach ($bag->getClassAnnotations() as $classname => $parameters)
        {
            if (!$this->annotationExists($classname))
            {
                throw new LogicException(sprintf("Annotation '@%s' could not be loaded. Maybe you need to add/remove a leading slash.", $classname));
            }
            
            $annotation = $this->factory->instantiate($bag->getOwner(), $classname, $parameters);
        }
    }
    
    private function annotationExists(string $classname)
    {
//        echo print_r($classname, true).PHP_EOL;
        
        if (class_exists($classname, false))
        {
            return true;
        }
        
        if ($this->hasNamespace($classname))
        {
            spl_autoload_call($classname);

            return class_exists($classname, false);
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
    
}

?>
