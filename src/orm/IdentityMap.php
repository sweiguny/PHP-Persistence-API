<?php

namespace PPA\orm;

use LogicException;
use PPA\orm\entity\Serializable;

class IdentityMap
{
    /**
     *
     * @var array
     */
    private $map = [];
    
    public function __construct()
    {
        
    }
    
    public function add($entity, $key)
    {
        $classname = get_class($entity);
        
        if (!isset($this->map[$classname]))
        {
            $this->map[$classname] = [];
        }
        
        if (isset($this->map[$classname][$key]))
        {
            throw new LogicException("'{$classname}' with key '{$key}' already in " . __CLASS__ . ".");
        }
        
        $this->map[$classname][$key] = $entity;
    }
    
    public function retrieve($classname, $key): Serializable
    {
        if (isset($this->map[$classname]) && isset($this->map[$classname][$key]))
        {
            return $this->map[$classname][$key];
        }
        else
        {
            throw new LogicException("'{$classname}' with key '{$key}' not in " . __CLASS__ . ".");
        }
    }
    
    public function remove($classname, $key)
    {
        if (isset($this->map[$classname]) && isset($this->map[$classname][$key]))
        {
            unset($this->map[$classname][$key]);
        }
        else
        {
            throw new LogicException("'{$classname}' with key '{$key}' not in " . __CLASS__ . ".");
        }
    }
    
}

?>
