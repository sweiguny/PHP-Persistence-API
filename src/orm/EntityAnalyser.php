<?php

namespace PPA\orm;

use PPA\core\exceptions\ExceptionFactory;
use PPA\orm\entity\Serializable;
use PPA\orm\mapping\AnnotationLoader;
use PPA\orm\mapping\AnnotationReader;
use PPA\orm\mapping\annotations\Column;
use PPA\orm\mapping\annotations\Id;
use PPA\orm\mapping\annotations\Table;
use ReflectionClass;

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
    
    public function getMetaData(Serializable $entity): Analysis
    {
        $classname = get_class($entity);
        $metadata  = $this->metaDataMap->retrieve($classname);
        
        if ($metadata == null)
        {
            $metadata = $this->analyse($entity, $classname);
            $this->metaDataMap->add($classname, $metadata);
        }
        
        return $metadata;
    }
    
    public function analyse(Serializable $entity, string $classname): Analysis
    {
        $this->reflector = new ReflectionClass($classname);
        $annotationBag   = $this->annotationLoader->load($this->annotationReader->read($entity));
        
        list($tableName) = $this->analyseClassAnnotations($classname, $annotationBag->getClassAnnotations());
        list($primaryProperty, $propertiesByName, $propertiesByColumn) = $this->analysePropertyAnnotations($classname, $annotationBag->getPropertyAnnotations());
        
        $analysis = new Analysis(
                $classname,
                $primaryProperty,
                $tableName,
                $propertiesByName,
                $propertiesByColumn
            );
        
        return $analysis;
    }

    private function analyseClassAnnotations(string $classname, array $classAnnotations): array
    {
//        print_r($classAnnotations);
        
        if (!isset($classAnnotations[Table::class]))
        {
            throw ExceptionFactory::TableAnnotationMissing($classname);
        }
        
        /* @var $table Table */
        $table = $classAnnotations[Table::class];
        
        return [
            $table->getName()
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
