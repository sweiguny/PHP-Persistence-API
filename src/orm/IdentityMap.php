<?php

namespace PPA\orm;

use PPA\core\exceptions\ExceptionFactory;
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
    
    public function add(Serializable $entity, $key)
    {
        $classname = get_class($entity);
        
        if (!isset($this->map[$classname]))
        {
            $this->map[$classname] = [];
        }
        
        if (isset($this->map[$classname][$key]))
        {
            throw ExceptionFactory::AlreadyExistentInIdentityMap($classname, $key);
        }
        
        $this->map[$classname][$key] = $entity;
    }
    
    public function retrieve(string $classname, $key): ?Serializable
    {
        if (isset($this->map[$classname]) && isset($this->map[$classname][$key]))
        {
            return $this->map[$classname][$key];
        }
        else
        {
            return null;
            //throw ExceptionFactory::NotExistentInIdentityMap($classname, $key);
        }
    }
    
    public function remove(Serializable $entity, $key)
    {
        $classname = get_class($entity);
        
        if (isset($this->map[$classname]) && isset($this->map[$classname][$key]))
        {
            unset($this->map[$classname][$key]);
        }
        else
        {
            throw ExceptionFactory::NotExistentInIdentityMap($classname, $key);
        }
    }
    
    public function dumpMapByObjectId(): array
    {
        $dump = [];
        
        foreach ($this->map as $classname => $listByPrimary)
        {
            foreach ($listByPrimary as $key => $entity)
            {
                $oid        = spl_object_hash($entity);
                $dump[$oid] = $entity;
            }
        }
        
        return $dump;
    }
    
}

?>
