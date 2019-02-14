<?php

namespace PPA\orm\mapping;

use PPA\core\exceptions\ExceptionFactory;
use ReflectionClass;

/**
 * Read annotatons from the entities.
 */
class AnnotationReader
{
    const PATTERN_ANNOTATIONS = "#\@([\w\\\\]+)[\s]*\(?(.*)\)?#";
    const PATTERN_PARAMETERS  = "#[\s]*([\w]+)[\s]*=[\s]*[\"\']([\s\w\\\\%]+)[\"\']#";
    
    
    
    private static $ignoredAnnotations = [
        // Annotation tags
//        "Annotation",       "Attribute",        "Attributes",
//        "Required",         "Target",
        
        // Widely used tags (but not existent in phpdoc)
        "fix",              "fixme",            "override",
        
        // PHPDocumentor 1 tags
        "abstract",         "access",           "code",
        "deprec",           "endcode",          "exception",
        "ingroup",          "inheritDoc",       "inheritdoc",
        "magic",            "name",             "toc",
        "tutorial",         "private",          "final",
        "static",           "staticvar",        "staticVar",
        "throw",
        
        // PHPDocumentor 2 tags.
        "api",              "author",           "category",
        "copyright",        "deprecated",       "example",
        "filesource",       "global",           "ignore",
        "internal",         "license",          "link",
        "method",           "package",          "param", 
        "property",         "property-read",    "property-write",
        "return",           "see",              "since",
        "source",           "subpackage",       "throws",
        "todo",             "TODO",             "usedby",
        "uses",             "var",              "version",
        
        // PHPUnit tags
        "codeCoverageIgnore", "codeCoverageIgnoreStart", "codeCoverageIgnoreEnd",
        
        // PHPCheckStyle
        "SuppressWarnings",
        
        // PHPStorm
        "noinspection",
        
        // PEAR
        "package_version",
        
        // PlantUML
        "startuml", "enduml"
    ];
    
    public static function addIgnore(string $annotation): void
    {
        if (!in_array($annotation, self::$ignoredAnnotations))
        {
            self::$ignoredAnnotations[] = $annotation;
        }
    }
    
    public function readFromAnnotatableClass(string $classname): AnnotationBag
    {
        $reflectionClass = new ReflectionClass($classname);
        
        if (!$reflectionClass->isSubclassOf(Annotatable::class))
        {
            throw ExceptionFactory::InvalidArgument("Class '{$classname}' is not a subclass of Annotatable.");
        }
        
        return $this->readAnnotations($reflectionClass);
    }
    
    public function readFromObject(Annotatable $annotatable): AnnotationBag
    {
        $reflectionClass = new ReflectionClass($annotatable);
        
        return $this->readAnnotations($reflectionClass);
    }
    
    private function readAnnotations(ReflectionClass $reflectionClass): AnnotationBag
    {
        $classAnnotations    = $this->fetchAnnotations($reflectionClass->getDocComment());
        $propertyAnnotations = $this->readPropertyAnnotations($reflectionClass->getProperties());

        return new AnnotationBag($classAnnotations, $propertyAnnotations); 
    }


    private function readPropertyAnnotations(array $properties): array
    {
        $result = [];
        
        foreach ($properties as $property)
        {
            $propName    = $property->getName();
            $annotations = $this->fetchAnnotations($property->getDocComment());
            
            $result[$propName] = $annotations;
        }
        
        return $result;
    }
    
    /**
     * 
     * @param string $docComment
     * @return array
     */
    private function fetchAnnotations(string $docComment): array
    {
        $matches     = [];
        $extracted   = [];
        $annotations = $this->extractAnnotations($docComment);

        foreach ($annotations as $annotation)
        {
            if (preg_match(self::PATTERN_ANNOTATIONS, $annotation, $matches))
            {
                $annotationClass  = $matches[1];
                $annotationParams = explode(",", $matches[2]);
                
                $extracted[$annotationClass] = [];
                
                foreach ($annotationParams as $parameter)
                {
                    if (preg_match(self::PATTERN_PARAMETERS, $parameter, $matches))
                    {
                        $paramKey = strtolower($matches[1]);
                        $paramVal = $matches[2];
                        
                        $extracted[$annotationClass][$paramKey] = $paramVal;
                    }
                }
            }
        }

        return $extracted;
    }

    /**
     * Splits, trims and filters the documentation to extract the annotations.
     * 
     * @param string $docComment The documentation of the class or property.
     * @return array
     */
    private function extractAnnotations(string $docComment): array
    {
        $split = explode("*", substr($docComment, 3, -2)); // removes /** and */
        $split = array_map("trim", $split);
        $split = array_filter($split, [$this, "filterAnnotations"]);
        
        return $split;
    }
    
    /**
     * @param string $value
     * @return boolean
     */
    private function filterAnnotations(string $value): bool
    {
        // omit empty rows
        if (empty($value))
        {
            return false;
        }
        
        // omit rows that do not begin with the indicator
        if (strpos($value, Annotation::INDICATOR) !== 0)
        {
            return false;
        }
        
        // check if annotation has to be ignored
        foreach (self::$ignoredAnnotations as $ignore)
        {
            if (strpos($value, $ignore) === 1)
            {
                return false;
            }
        }
        
        return true;
    }
    
}

?>
