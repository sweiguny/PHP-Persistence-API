<?php

namespace PPA\core\exceptions;

use PPA\core\exceptions\io\NotADirectoryException;
use PPA\core\exceptions\logic\AlreadyExistentInIdentityMapException;
use PPA\core\exceptions\logic\AlreadyExistentInOriginsMapException;
use PPA\core\exceptions\logic\CouldNotLoadAnnotationException;
use PPA\core\exceptions\logic\DatatypeDoesNotExistException;
use PPA\core\exceptions\logic\InvalidArgumentException;
use PPA\core\exceptions\logic\NotExistentInIdentityMapException;
use PPA\core\exceptions\logic\NotExistentInOriginsMapException;
use PPA\core\exceptions\logic\NotSerializableException;
use PPA\core\exceptions\logic\ParameterRequiredException;
use PPA\core\exceptions\logic\TableAnnotationMissingException;
use PPA\core\exceptions\logic\TargetAnnotationNotExistentException;
use PPA\core\exceptions\logic\TypeDirectoryAlreadyConsideredException;
use PPA\core\exceptions\logic\UnknownCascadeTypeException;
use PPA\core\exceptions\logic\UnknownFetchTypeException;
use PPA\core\exceptions\logic\UnknownParametersException;
use PPA\core\exceptions\logic\WrongTargetClassException;
use PPA\core\exceptions\logic\WrongTargetPropertyException;
use PPA\core\exceptions\runtime\CollectionStateException;
use PPA\core\exceptions\runtime\InvalidQueryBuilderStateException;
use PPA\core\exceptions\runtime\InvalidQueryBuilderTypeException;
use PPA\orm\entity\Serializable;
use PPA\orm\mapping\Annotation;
use PPA\orm\mapping\types\AbstractType;


final class ExceptionFactory
{
    
    private function __construct() {}
    
    public static function UnknownParameters(array $parameters, string $annotationClass, string $entityClass): UnknownParametersException
    {
        return new UnknownParametersException("Unknown parameter(s) ['" . implode("', '", array_keys($parameters)) . "'] of Annotation '@{$annotationClass}' used in entity class '{$entityClass}'.");
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
        return new UnknownCascadeTypeException("The cascade type is '{$cascade}', but must be one of these: ['" . implode("', '", $cascadeTypes) . "']");
    }
    
    public static function UnknownFetchType(string $fetch, array $fetchTypes): UnknownFetchTypeException
    {
        return new UnknownFetchTypeException("The cascade type is '{$fetch}', but must be one of these: ['" . implode("', '", $fetchTypes) . "']");
    }
    
    public static function NotSerializable(string $mappedClass): NotSerializableException
    {
        return new NotSerializableException(sprintf("Mapped class '%s' must implement interface '%s'.", $mappedClass, Serializable::class));
    }
    
    public static function CouldNotLoadAnnotation(string $annotationClassname, string $ownerClassname): CouldNotLoadAnnotationException
    {
        return new CouldNotLoadAnnotationException(sprintf("Annotation '@%s' (set on class '%s') could not be loaded. Maybe you need to add/remove a leading slash.", $annotationClassname, $ownerClassname));
    }
    
    public static function TableAnnotationMissing(string $entityClassname): TableAnnotationMissingException
    {
        return new TableAnnotationMissingException(sprintf("@Table Annotation is missing on Entity class '%s'.", $entityClassname));
    }
    
    public static function NotExistentInIdentityMap(string $classname, $key): NotExistentInIdentityMapException
    {
        return new NotExistentInIdentityMapException(sprintf("'{$classname}' with key '{$key}' not in IdentityMap.", $classname, $key));
    }
    
    public static function AlreadyExistentInIdentityMap(string $classname, $key): AlreadyExistentInIdentityMapException
    {
        return new AlreadyExistentInIdentityMapException(sprintf("'{$classname}' with key '{$key}' already in IdentityMap.", $classname, $key));
    }
    
    public static function NotExistentInOriginsMap(string $classname, $key): NotExistentInOriginsMapException
    {
        return new NotExistentInOriginsMapException(sprintf("'{$classname}' with key '{$key}' not in OriginsMap.", $classname, $key));
    }
    
    public static function AlreadyExistentInOriginsMap(string $classname, $key): AlreadyExistentInOriginsMapException
    {
        return new AlreadyExistentInOriginsMapException(sprintf("'{$classname}' with key '{$key}' already in OriginsMap.", $classname, $key));
    }
    
    public static function NotADirectory(string $path): NotADirectoryException
    {
        return new NotADirectoryException(sprintf("Path '%s' is not a directory.", $path));
    }
    
    public static function TypeDirectoryAlreadyConsidered(string $path): TypeDirectoryAlreadyConsideredException
    {
        return new TypeDirectoryAlreadyConsideredException(sprintf("Type directory '%s' is already considered.", $path));
    }
    
    public static function DatatypeDoesNotExist(string $datatype): DatatypeDoesNotExistException
    {
        $message = "Column datatype '%s' does not exist. Practically, there should be a class named '%s'."
                 . " \n" . "You can create your own datatype(s) by extending class '%s' and register the directory that contains the datatype(s) by calling 'TypeMapper::registerTypeDirectory()'."
                ;
        
        return new DatatypeDoesNotExistException(sprintf($message, $datatype, "Type".ucfirst($datatype), AbstractType::class));
    }
    
    public static function InvalidArgument(string $message): InvalidArgumentException
    {
        return new InvalidArgumentException($message);
    }
    
    public static function InvalidQueryBuilderState(int $requestedState, int $currentState, string $additionalMessage = null): InvalidQueryBuilderStateException
    {
        return new InvalidQueryBuilderStateException(sprintf("QueryBuilder is not in required state '%s', but is '%s'.", $requestedState, $currentState) . ($additionalMessage == null ? "" : " " . $additionalMessage));
    }
    
    public static function InvalidQueryBuilderType(string $requestedType, string $currentType): InvalidQueryBuilderTypeException
    {
        return new InvalidQueryBuilderTypeException(sprintf("QueryBuilder does not work on required type '%s', but on '%s'.", $requestedType, $currentType));
    }
    
    public static function CollectionState(string $message): CollectionStateException
    {
        return new CollectionStateException($message);
    }
    
//    public static function UnknownInternalDatatype(string $datatype, string $parameterName, string $annotationClass): UnknownInternalDatatypeException
//    {
//        return new UnknownInternalDatatypeException(sprintf("Unknown internal datatype '%s' defined for parameter '%s' in Annotation '@%s'. Can only be of of these: %s", $datatype, $parameterName, $annotationClass, "['" . implode("', '", Annotation::INTERNAL_DATATYPES) . "']"));
//    }
}

?>
