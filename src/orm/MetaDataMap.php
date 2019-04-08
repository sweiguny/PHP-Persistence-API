<?php

namespace PPA\orm;

use PPA\core\exceptions\ExceptionFactory;

class MetaDataMap
{
    /**
     *
     * @var array
     */
    private $map = [];
    
    public function add($classname, Analysis $metadata): void
    {
        if (isset($this->map[$classname]))
        {
            throw ExceptionFactory::InvalidArgument("There is already an entry for class '{$classname}' in " . __CLASS__ . ".");
        }
        else
        {
            $this->map[$classname] = $metadata;
        }
    }
    
    public function retrieve($classname): ?Analysis
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
    
    public function remove($classname): void
    {
        if (isset($this->map[$classname]))
        {
            unset($this->map[$classname]);
        }
        else
        {
            throw ExceptionFactory::InvalidArgument("For class '{$classname}' was no entry in " . __CLASS__ . " found.");
        }
    }
    
}

?>
