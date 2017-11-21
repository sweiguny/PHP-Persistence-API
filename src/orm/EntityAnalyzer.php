<?php

namespace PPA\orm;

use PPA\orm\entity\Serializable;
use PPA\orm\mapping\AnnotationBag;
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
    
    public function analyze(Serializable $entity): AnnotationBag
    {
        $annotationBag = $this->annotationLoader->load($this->annotationReader->read($entity));
        
        
        
        return $annotationBag;
    }
    
    public function getMetaData(Serializable $entity): AnnotationBag
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
