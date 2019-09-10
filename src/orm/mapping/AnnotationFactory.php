<?php

namespace PPA\orm\mapping;

use LogicException;
use PPA\core\exceptions\ExceptionFactory;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;

class AnnotationFactory
{
    /**
     *
     * @var AnnotationReader
     */
    private $annotationReader;
    
    public function __construct()
    {
        $this->annotationReader = new AnnotationReader();
    }
    
    public function instantiate(string $annotatableClass, string $annotationClassname, array $parameters, string $propertyName = null): Annotation
    {
        $reflector   = new ReflectionClass($annotationClassname);
        $constructor = $reflector->getConstructor();
        $description = $this->annotationReader->readFromAnnotatableClass($annotationClassname);
        
        $this->workOnParameters($annotatableClass, $annotationClassname, $description, $parameters, $propertyName);
        
        /* @var $annotation Annotation */
        $annotation = $constructor == null
                ? $reflector->newInstanceWithoutConstructor()
                : $reflector->newInstanceArgs($this->getConstructorParameters($constructor->getParameters(), $parameters));
        
        $this->setProperties($annotation, $reflector, $parameters);
        
        if (!empty($parameters))
        {
            throw ExceptionFactory::UnknownParameters($parameters, $annotationClassname, $annotatableClass);
        }
        
        return $annotation;
    }
    
    public function getConstructorParameters(array $constructorParameters, array &$annotationParameters): array
    {
        $parameters = [];
        
        /* @var $parameter ReflectionParameter */
        foreach ($constructorParameters as $parameter)
        {
            $parameters[] = $annotationParameters[$parameter->getName()];
            unset($annotationParameters[$parameter->getName()]);
        }
        
        return $parameters;
    }
    
    private function setProperties(Annotation $annotation, ReflectionClass $reflector, array &$annotationParameters): void
    {
        foreach ($annotationParameters as $key => $parameter)
        {
            if ($reflector->hasProperty($key))
            {
                $property   = $reflector->getProperty($key);
                $setterName = "set" . ucfirst($key);

                try // to user setter method
                {
                    $setter = $reflector->getMethod($setterName);
                    $setter->invoke($annotation, $parameter);
                }
                catch (ReflectionException $exc) // when there's no setter-method.
                {
                    /**
                     * If there is no setter method an exception is thrown
                     * and we inject the property directly.
                     */

                    $property->setAccessible(true);
                    $property->setValue($annotation, $parameter);
                }
                finally
                {
                    unset($annotationParameters[$key]);
                }
            }
        }
    }
    
    /**
     * This method checks for required parameters of the defined annotations.
     * It also parses the default values.
     * 
     * @param string $annotatableClass
     * @param RawAnnotationBag $annotationDescription
     * @param array $annotationParameters
     * @param string $propertyName
     * @throws LogicException
     */
    private function workOnParameters(string $annotatableClass, string $annotationClassname, AnnotationBag $annotationDescription, array &$annotationParameters, string $propertyName = null): void
    {
        $classAnnotations    = $annotationDescription->getClassAnnotations();
        $propertyAnnotations = $annotationDescription->getPropertyAnnotations();
        
        if (!isset($classAnnotations[Annotation::TARGET]) && !isset($classAnnotations[Annotation::TARGET]["value"]))
        {
            throw ExceptionFactory::TargetAnnotationNotExistent($annotationClassname);
        }
        
        $target = $classAnnotations[Annotation::TARGET]["value"];
        
        if ($propertyName == null && $target != Annotation::TARGET_CLASS)
        {
            throw ExceptionFactory::WrongTargetClass($annotationClassname, $annotatableClass, $target);
        }
        else if ($propertyName != null && $target != Annotation::TARGET_PROPERTY)
        {
            throw ExceptionFactory::WrongTargetProperty($annotationClassname, $annotatableClass, $propertyName, $target);
        }
        
        /*
         * Check datatypes and requirement of parameters 
         */
        foreach ($propertyAnnotations as $parameterName => $value)
        {
            $parameter = $value["Parameter"];
            $datatype  = DataTypeMapper::mapDatatype($parameter["datatype"]);
            
            if (isset($parameter["required"]) && !isset($annotationParameters[$parameterName]))
            {
                if (!isset($parameter["default"]))
                {
                    throw ExceptionFactory::ParameterRequired($parameterName, $annotationClassname, $annotatableClass);
                }
                
                $annotationParameters[$parameterName] = $this->parseDefault($annotatableClass, $parameter["default"], $propertyName);
            }
            
            if (isset($annotationParameters[$parameterName]))
            {
                $datatype->convertValue($annotationParameters[$parameterName]);
            }
        }
        
        /*
         * Other checks?
         */
//        foreach ($annotationParameters as $parameter)
//        {
//            
//        }
    }
    
    private function parseDefault(string $annotatableClass, $value, string $propertyName = null)
    {
        switch ($value)
        {
            case "%classname%":
                $classname = explode("\\", $annotatableClass);
                $value     = strtolower(array_pop($classname));
                break;
            case "%propertyname%":
                $value     = strtolower($propertyName);
                break;
            default:
                break;
        }
        
        return $value;
    }
    
}

?>
