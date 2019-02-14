<?php

namespace PPA\orm\mapping;

use InvalidArgumentException;
use PPA\core\exceptions\ExceptionFactory;

/**
 * Loads annotations of the entities.
 */
class AnnotationLoader
{
    
    const PPA_ANNOTATION_PATH = __DIR__ . DIRECTORY_SEPARATOR . "annotations";
    
    private static $includePaths = [
        self::PPA_ANNOTATION_PATH
    ];

    /**
     * Contains all unqualified class names that were resolved once.
     * This accelerates the class loading process, because the process of
     * loading and processing the php-file can be omitted.
     * 
     * @var array
     */
    private static $unqualifiedClasses = [];


    public static function addIncludePath(string $path): void
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
    
    public function load(string $annotatableClass, RawAnnotationBag $bag): AnnotationBag
    {
        return new AnnotationBag(
                $this->loadClassAnnotations($annotatableClass, $bag),
                $this->loadPropertyAnnotations($annotatableClass, $bag)
            );
    }
    
    private function loadClassAnnotations(string $annotatableClass, RawAnnotationBag $bag): array
    {
        return $this->iterateOverAnnotations($annotatableClass, $bag->getClassAnnotations());
    }
    
    private function iterateOverAnnotations(string $annotatableClass, array $annotations, string $propertyName = null)
    {
        $loadedAnnotations = [];
        
        foreach ($annotations as $annotationClassname => $parameters)
        {
            $resolvedClassname = ltrim($this->loadAndResolveAnnotationClassname($annotationClassname, $annotatableClass), "\\");
            $annotation        = $this->factory->instantiate($annotatableClass, $resolvedClassname, $parameters, $propertyName);
            
            if ($propertyName == null)
            {
                $loadedAnnotations[$resolvedClassname] = $annotation;
            }
            else
            {
                $loadedAnnotations[$propertyName][$resolvedClassname] = $annotation;
            }
        }
        
        return $loadedAnnotations;
    }

    private function loadPropertyAnnotations(string $annotatableClass, RawAnnotationBag $bag): array
    {
        $loadedAnnotations = [];
        
        foreach ($bag->getPropertyAnnotations() as $propertyName => $annotations)
        {
            $loadedAnnotations[$propertyName] = $this->iterateOverAnnotations($annotatableClass, $annotations, $propertyName);
        }
        
        return $loadedAnnotations;
    }

    private function loadAndResolveAnnotationClassname(string $annotationClassname, string $annotatableClass): string
    {
        if (class_exists($annotationClassname, false))
        {
            return $annotationClassname;
        }
        
        if ($this->hasNamespace($annotationClassname) && class_exists($annotationClassname))
        {
            return $annotationClassname;
        }
        else
        {
            if (isset(self::$unqualifiedClasses[$annotationClassname]))
            {
                return self::$unqualifiedClasses[$annotationClassname];
            }
            else
            {
                foreach (self::$includePaths as $path)
                {
//                    echo "###########{$annotationClassname}###########\n";

                    $file = $path . DIRECTORY_SEPARATOR . $annotationClassname . ".php";

                    if (is_file($file))
                    {
//                        echo $file . "\n";

                        $resolvedClassname = $this->getFullyQualifiedClassname($file);
                        self::$unqualifiedClasses[$annotationClassname] = $resolvedClassname;
                        
                        require_once $file;
                        return $resolvedClassname;
                    }
                }
            }
            
            throw ExceptionFactory::CouldNotLoadAnnotation($annotationClassname, $annotatableClass);
        }
    }
    
    private function getFullyQualifiedClassname(string $path): string
    {
        $contents = file_get_contents($path);
        $tokens   = token_get_all($contents);

        $namespace = "";
        $class     = "";
        
        $fetchNamespace = false;
        $fetchClass     = false;
        
        foreach ($tokens as $token)
        {
            if (is_array($token) && $token[0] == T_NAMESPACE)
            {
                $fetchNamespace = true;
            }

            if (is_array($token) && $token[0] == T_CLASS)
            {
                $fetchClass = true;
            }

            if ($fetchNamespace)
            {
                if (is_array($token) && in_array($token[0], [T_STRING, T_NS_SEPARATOR]))
                {
                    $namespace .= $token[1];
                }
                else if ($token === ";")
                {
                    $fetchNamespace = false;
                }
            }

            if ($fetchClass)
            {
                if (is_array($token) && $token[0] == T_STRING)
                {
                    $class = $token[1];
                    break;
                }
            }
        }

        return $namespace ? $namespace . '\\' . $class : $class;
    }

    private function hasNamespace(string $classname): bool
    {
        $splitted = explode("\\", $classname);

        return count($splitted) != 1;
    }
    
}

?>
