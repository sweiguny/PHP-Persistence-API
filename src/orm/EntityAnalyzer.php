<?php

namespace PPA\orm;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\AnnotationLoader;
use PPA\orm\mapping\AnnotationReader;

class EntityAnalyzer
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
    
    public function analyze(Serializable $entity): array
    {
        $annotations = $this->annotationReader->read($entity);
        $this->annotationLoader->load($annotations);
        
        
        $analysis = [];
        
        return $analysis;
    }
    
    public function getMetaData(Serializable $entity): array
    {
        $classname = get_class($entity);
        $metadata  = $this->metaDataMap->retrieve($classname);
        
        if ($metadata == null)
        {
            $metadata = $this->analyze($classname);
            $this->metaDataMap->add($classname, $metadata);
        }
        
        return $metadata;
    }
}

?>
