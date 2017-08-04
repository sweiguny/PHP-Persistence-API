<?php

namespace PPA\orm\mapping;

use DomainException;
use PPA\orm\entity\Serializable;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use ReflectionProperty;

class AnnotationFactory
{
    
    public function instantiate(Serializable $entity, string $classname, array $parameters): Annotation
    {
        $reflector = new ReflectionClass($classname);
        
        $constructor = $reflector->getConstructor();
        
        /* @var $annotation Annotation */
        $annotation = $constructor == null
                ? $reflector->newInstanceWithoutConstructor()
                : $reflector->newInstanceArgs($this->getConstructorParameters($constructor->getParameters(), $parameters));
        
        $this->setProperties($annotation, $reflector, $parameters);
        
        if (!empty($parameters))
        {
            throw new DomainException("Unknown parameter(s) '" . implode("', '", array_keys($parameters)) . "' of Annotation '@{$classname}' used in entity class '" . get_class($entity) . "'.");
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
                 * If there is no setter method an exception is thrown.
                 * To avoid catching other exceptions than the exptected one,
                 * the exception message is checked (since there is no exception code).
                 * 
                 * If there is no setter method, we inject the property directly.
                 */
                if ($exc->getMessage() == "Method {$setterName} does not exist")
                {
                    $property->setAccessible(true);
                    $property->setValue($annotation, $annotationParameters[$propName]);
                }
                else
                {
                    throw $exc;
                }
            }
            finally
            {
                unset($annotationParameters[$propName]);
            }
        }
    }
    
}

?>
