<?php

namespace PPA\orm\mapping;

use LogicException;
use PPA\core\exceptions\ExceptionFactory;
use PPA\orm\entity\Serializable;
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
    
    public function instantiate(Serializable $entity, string $annotationClassname, array $parameters, string $propertyName = null): Annotation
    {
        $reflector   = new ReflectionClass($annotationClassname);
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
            throw ExceptionFactory::UnknownParameters($parameters, $annotationClassname, get_class($entity));
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
                     * If there is no setter method
                     * an exception is thrown
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
     * @param Serializable $entity
     * @param RawAnnotationBag $annotationDescription
     * @param array $annotationParameters
     * @param string $propertyName
     * @throws LogicException
     */
    private function workOnParameters(Serializable $entity, RawAnnotationBag $annotationDescription, array &$annotationParameters, string $propertyName = null): void
    {
        $classAnnotations    = $annotationDescription->getClassAnnotations();
        $propertyAnnotations = $annotationDescription->getPropertyAnnotations();
        $annotationClass     = get_class($annotationDescription->getOwner());
        $entityClass         = get_class($entity);
        
        if (!isset($classAnnotations[Annotation::TARGET]) && !isset($classAnnotations[Annotation::TARGET]["value"]))
        {
            throw ExceptionFactory::TargetAnnotationNotExistent($annotationClass);
        }
        
        $target = $classAnnotations[Annotation::TARGET]["value"];
        
        if ($propertyName == null && $target != Annotation::TARGET_CLASS)
        {
            throw ExceptionFactory::WrongTargetClass($annotationClass, $entityClass, $target);
        }
        else if ($propertyName != null && $target != Annotation::TARGET_PROPERTY)
        {
            throw ExceptionFactory::WrongTargetProperty($annotationClass, $entityClass, $propertyName, $target);
        }
        
        /*
         * Check datatypes and requirement of parameters 
         */
        foreach ($propertyAnnotations as $parameterName => $value)
        {
            $parameter = $value["Parameter"];
            $datatype  = $parameter["datatype"];
            
            if (isset($parameter["required"]) && !isset($annotationParameters[$parameterName]))
            {
                if (!isset($parameter["default"]))
                {
                    throw ExceptionFactory::ParameterRequired($parameterName, $annotationClass, $entityClass);
                }
                
                $annotationParameters[$parameterName] = $this->parseDefault($entity, $parameter["default"], $propertyName);
            }
            
            if (isset($annotationParameters[$parameterName]))
            {
                if (!in_array($datatype, Annotation::INTERNAL_DATATYPES))
                {
                    throw ExceptionFactory::UnknownInternalDatatype($datatype, $parameterName, $annotationClass);
                }
                
                $result = settype($annotationParameters[$parameterName], $datatype);
                var_dump($result);
//                use ctype!
                
//                $value = $annotationParameters[$parameterName];
//                
//                switch ($datatype)
//                {
//                    case Annotation::DATATYPE_STRING:
//                        $value = (string)$value;
//                        break;
//                    case Annotation::DATATYPE_INTEGER:
//                        $value = (integer)$value;
//                    default:
//                        throw ExceptionFactory::UnknownInternalDatatype($datatype, $parameterName, $annotationClass);
//                        break;
//                }
//                
//                $annotationParameters[$parameterName] = $value;
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
