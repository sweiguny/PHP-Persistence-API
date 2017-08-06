<?php

namespace PPA\orm\mapping;

use LogicException;
use PPA\core\exceptions\ExceptionFactory;
use PPA\orm\entity\Serializable;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ReflectionProperty;

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
    
    public function instantiate(Serializable $entity, string $classname, array $parameters, string $propertyName = null): Annotation
    {
        $reflector   = new ReflectionClass($classname);
        $constructor = $reflector->getConstructor();
        $description = $this->annotationReader->read($reflector->newInstanceWithoutConstructor());
        
        $this->workOnParameters($entity, $description, $parameters, $propertyName);
        
        /* @var $annotation Annotation */
        $annotation = $constructor == null
                ? $reflector->newInstanceWithoutConstructor()
                : $reflector->newInstanceArgs($this->getConstructorParameters($constructor->getParameters(), $parameters));
        
        $this->setProperties($annotation, $reflector, $parameters);
        
        if (!empty($parameters))
        {
            throw ExceptionFactory::UnknownParameters($parameters, $classname, get_class($entity));
        }
        
        return $annotation;
    }
    
    private function getConstructorParameters(array $constructorParameters, array &$annotationParameters): array
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
    
    private function setProperties(Annotation $annotation, ReflectionClass $reflector, array &$annotationParameters)
    {
        $properties = $reflector->getProperties();
        
        /* @var $property ReflectionProperty */
        foreach ($properties as $property)
        {
            $propName   = $property->getName();
            $setterName = "set" . ucfirst($propName);
            
            try // to user setter method
            {
                $setter = $reflector->getMethod($setterName);
                $setter->invoke($annotation, $annotationParameters[$propName]);
            }
            catch (ReflectionException $exc) // when there's no setter-method.
            {
                /**
                 * If there is no setter method
                 * an exception is thrown
                 * and we inject the property directly.
                 */
                
                $property->setAccessible(true);
                $property->setValue($annotation, $annotationParameters[$propName]);
            }
            finally
            {
                unset($annotationParameters[$propName]);
            }
        }
    }
    
    /**
     * This method checks for required parameters of the defined annotations.
     * It also parses the default values.
     * 
     * @param Serializable $entity
     * @param AnnotationBag $annotationDescription
     * @param array $annotationParameters
     * @param string $propertyName
     * @throws LogicException
     */
    private function workOnParameters(Serializable $entity, AnnotationBag $annotationDescription, array &$annotationParameters, string $propertyName = null)
    {
        $classAnnotations    = $annotationDescription->getClassAnnotations();
        $propertyAnnotations = $annotationDescription->getPropertyAnnotations();
        
        
        if (!isset($classAnnotations[Annotation::TARGET]) && !isset($classAnnotations[Annotation::TARGET]["value"]))
        {
            throw ExceptionFactory::TargetAnnotationNotExistent(get_class($annotationDescription->getOwner()));
        }
        
        $target = $classAnnotations[Annotation::TARGET]["value"];
        
        if ($propertyName == null && $target != Annotation::TARGET_CLASS)
        {
            throw ExceptionFactory::WrongTargetClass(get_class($annotationDescription->getOwner()), get_class($entity), $target);
        }
        else if ($propertyName != null && $target != Annotation::TARGET_PROPERTY)
        {
            throw ExceptionFactory::WrongTargetProperty(get_class($annotationDescription->getOwner()), get_class($entity), $propertyName, $target);
        }
        
        /*
         * Check for required parameters
         */
        foreach ($propertyAnnotations as $key => $value)
        {
            $parameter = $value["Parameter"];
            
            if (isset($parameter["required"]) && !isset($annotationParameters[$key]))
            {
                if (!isset($parameter["default"]))
                {
                    throw ExceptionFactory::ParameterRequired($key, get_class($annotationDescription->getOwner()), get_class($entity));
                }
                
                $annotationParameters[$key] = $this->parseDefault($entity, $parameter["default"], $propertyName);
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
    
    private function parseDefault(Serializable $entity, $value, string $propertyName = null)
    {
        switch ($value)
        {
            case "%classname%":
                $classname = explode("\\", get_class($entity));
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
