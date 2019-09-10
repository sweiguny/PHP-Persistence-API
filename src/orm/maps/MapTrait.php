<?php

namespace PPA\orm\maps;

use PPA\orm\entity\Serializable;

trait MapTrait
{
    private function getImperatives(Serializable $entity): array
    {
        $classname = get_class($entity);
        
        $key = $this->analyser->getMetaData($classname)->getPrimaryProperty()->getValue($entity);
        
        return [$classname, $key];
    }
}

?>
