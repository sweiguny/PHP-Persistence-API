<?php

namespace PPA\core;

class Entity {
    
    public function getShortInfo() {
        $primaryProperty = EntityMetaDataMap::getInstance()->getPrimaryProperty(get_class($this));
        return "[" . $primaryProperty->getColumn() . ": " . $primaryProperty->getValue($this) . "]";
    }
    
}

?>
