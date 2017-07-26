<?php

namespace PPA\orm;

class EntityAnalyzer
{
    /**
     *
     * @var MetaDataMap
     */
    private $metaDataMap;
    
    public function __construct()
    {
        $this->metaDataMap = new MetaDataMap();
    }
    
    public function analyze(string $classname): array
    {
        $analysis = [];
        
        return $analysis;
    }
    
    public function getMetaData($entity): array
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
