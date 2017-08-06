<?php

namespace PPA\core\exceptions;

use PPA\core\exceptions\logic\ParameterRequiredException;
use PPA\core\exceptions\logic\TargetAnnotationNotExistentException;
use PPA\core\exceptions\logic\UnknownCascadeTypeException;
use PPA\core\exceptions\logic\UnknownFetchTypeException;
use PPA\core\exceptions\logic\UnknownParametersException;
use PPA\core\exceptions\logic\WrongTargetClassException;
use PPA\core\exceptions\logic\WrongTargetPropertyException;
use PPA\orm\mapping\Annotation;


final class ExceptionFactory
{
    
    private function __construct() {}
    
    public static function UnknownParameters(array $parameters, string $annotationClass, string $entityClass): UnknownParametersException
    {
        return new UnknownParametersException("Unknown parameter(s) '" . implode("', '", array_keys($parameters)) . "' of Annotation '@{$annotationClass}' used in entity class '{$entityClass}'.");
    }
    
    public static function TargetAnnotationNotExistent(string $annotationClass): TargetAnnotationNotExistentException
    {
        return new TargetAnnotationNotExistentException(sprintf("Annotation '@%s' or its parameter '%s' does not exist for Annotation '@%s'.", Annotation::TARGET, "value", $annotationClass));
    }
    
    public static function WrongTargetProperty(string $annotationClass, string $entityClass, string $propertyName, string $target): WrongTargetPropertyException
    {
        return new WrongTargetPropertyException(sprintf("The '@%s' for annotation '@%s' defined in class '%s' for property '%s' is '%s'.", Annotation::TARGET, $annotationClass, $entityClass, $propertyName, $target));
    }
    
    public static function WrongTargetClass(string $annotationClass, string $entityClass, string $target): WrongTargetClassException
    {
        return new WrongTargetClassException(sprintf("The '@%s' for annotation '@%s' defined for class '%s' is '%s'.", Annotation::TARGET, $annotationClass, $entityClass, $target));
    }
    
    public static function ParameterRequired(string $parameterName, string $annotationClass, string $entityClass): ParameterRequiredException
    {
        return new ParameterRequiredException(sprintf("Parameter '%s' of annotation '@%s' used in class '%s' is required and doesn't have a default value.", $parameterName, $annotationClass, $entityClass));
    }
    
    public static function UnknownCascadeType(string $cascade, array $cascadeTypes): UnknownCascadeTypeException
    {
        return new UnknownCascadeTypeException("The cascade type is '{$cascade}', but must be one of these: '" . implode("', '", $cascadeTypes) . "'");
    }
    
    public static function UnknownFetchType(string $fetch, array $fetchTypes): UnknownFetchTypeException
    {
        return new UnknownFetchTypeException("The cascade type is '{$fetch}', but must be one of these: '" . implode("', '", $fetchTypes) . "'");
    }
    
}

?>
