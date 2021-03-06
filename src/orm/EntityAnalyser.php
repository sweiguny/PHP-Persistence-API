<?php

namespace PPA\orm;

use PPA\core\exceptions\ExceptionFactory;
use PPA\orm\EntityProperty;
use PPA\orm\mapping\AnnotationLoader;
use PPA\orm\mapping\AnnotationReader;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Entity;
use PPA\orm\mapping\annotations\Id;

class EntityAnalyser
{
    /**
     *
     * @var MetaDataMap
     */
    private $metaDataMap;
    
    /**
     *
     * @var AnnotationReader
     */
    private $annotationReader;

    /**
     *
     * @var AnnotationLoader
     */
    private $annotationLoader;

    public function __construct()
    {
        $this->metaDataMap      = new MetaDataMap();
        $this->annotationReader = new AnnotationReader();
        $this->annotationLoader = new AnnotationLoader();
    }
    
    public function getMetaData(string $classname): Analysis
    {
//        $classname = get_class($entity);
        $metadata  = $this->metaDataMap->retrieve($classname);
        
        if ($metadata == null)
        {
            $metadata = $this->analyse($classname);
            $this->metaDataMap->add($classname, $metadata);
        }
        
        return $metadata;
    }
    
    public function analyse(string $classname): Analysis
    {
//        $this->reflector = new ReflectionClass($classname);
        $annotationBag   = $this->annotationLoader->load($classname, $this->annotationReader->readFromAnnotatableClass($classname));
        
        list($tableName, $repositoryClass) = $this->analyseClassAnnotations($classname, $annotationBag->getClassAnnotations());
        list($primaryProperty, $propertiesByName, $propertiesByColumn) = $this->analysePropertyAnnotations($classname, $annotationBag->getPropertyAnnotations());
        
        $analysis = new Analysis(
                $classname,
                $primaryProperty,
                $tableName,
                $repositoryClass,
                $propertiesByName,
                $propertiesByColumn
            );
        
        return $analysis;
    }

    private function analyseClassAnnotations(string $classname, array $classAnnotations): array
    {
//        print_r($classAnnotations);
        
        if (!isset($classAnnotations[Entity::class]))
        {
            throw ExceptionFactory::EntityAnnotationMissing($classname);
        }
        
        /* @var $entityAnnotation Entity */
        $entityAnnotation = $classAnnotations[Entity::class];
        
        return [
            $entityAnnotation->getTable(),
            $entityAnnotation->getRepositoryClass()
        ];
    }

    private function analysePropertyAnnotations(string $classname, array $propertyAnnotations): array
    {
//        print_r($propertyAnnotations);
        
        $propertiesByName   = [];
        $propertiesByColumn = [];
        $primaryProperty    = null;
        
        foreach ($propertyAnnotations as $propertyName => $annotations)
        {
            $property = new EntityProperty($classname, $propertyName);
            $property->setAccessible(true);
            
            /* @var $column Column */
            $column = $annotations[Column::class];
            $property->setColumn($column);
            
            if (isset($annotations[Id::class]))
            {
                /* @var $id Id */
                $id = $annotations[Id::class];
                $property->makePrimary();
                $primaryProperty = $property;
            }
            
            $propertiesByName[$propertyName]        = $property;
            $propertiesByColumn[$column->getName()] = $property;
        }
        
        return [$primaryProperty, $propertiesByName, $propertiesByColumn];
    }
    
}

?>
