<?php

namespace PPA\orm;

use InvalidArgumentException;

class MetaDataMap
{
    /**
     *
     * @var array
     */
    private $map = [];
    
    public function add($classname, array $metadata)
    {
        if (isset($this->map[$classname]))
        {
            throw new InvalidArgumentException("There is already an entry for class '{$classname}' in " . __CLASS__ . ".");
        }
        else
        {
            $this->map[$classname] = $metadata;
        }
    }
    
    public function retrieve($classname): array
    {
        if (isset($this->map[$classname]))
        {
            return $this->map[$classname];
        }
        else
        {
            return null;
        }
    }
    
    public function remove($classname)
    {
        if (isset($this->map[$classname]))
        {
            unset($this->map[$classname]);
        }
        else
        {
            throw new InvalidArgumentException("For class '{$classname}' was no entry in " . __CLASS__ . " found.");
        }
    }
    
}

?>
