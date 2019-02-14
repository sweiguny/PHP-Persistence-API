<?php

namespace PPA\orm;

use PPA\core\exceptions\ExceptionFactory;
use PPA\orm\entity\Serializable;

/**
 * Description of OriginsMap
 *
 * @author siwe
 */
class OriginsMap
{
    /**
     *
     * @var array
     */
    private $map = [];
    
    /**
     *
     * @var EntityAnalyser
     */
    private $analyser;

    public function __construct(EntityAnalyser $analyser)
    {
        $this->analyser = $analyser;
    }

    public function add(Serializable $entity, $key): void
    {
        $classname = get_class($entity);
        
        if (!isset($this->map[$classname]))
        {
            $this->map[$classname] = [];
        }
        
        if (isset($this->map[$classname][$key]))
        {
            throw ExceptionFactory::AlreadyExistentInOriginsMap($classname, $key);
        }
        
        $this->map[$classname][$key] = $this->extractData($entity);
    }
    
    public function retrieve(string $classname, $key): ?array
    {
        if (isset($this->map[$classname]) && isset($this->map[$classname][$key]))
        {
            return $this->map[$classname][$key];
        }
        else
        {
            return null;
        }
    }
    
    public function remove(Serializable $entity, $key): void
    {
        $classname = get_class($entity);
        
        if (isset($this->map[$classname]) && isset($this->map[$classname][$key]))
        {
            unset($this->map[$classname][$key]);
        }
        else
        {
            throw ExceptionFactory::NotExistentInOriginsMap($classname, $key);
        }
    }
    
    public function extractData(Serializable $entity): array
    {
        $classname  = get_class($entity);
        $properties = $this->analyser->getMetaData($classname)->getPropertiesByColumn();
        $data       = [];
        
        foreach ($properties as $property)
        {
            /* @var $property \PPA\core\EntityProperty */
            $data[$property->getName()] = $property->getValue($entity);
        }
        
        return $data;
    }
    
}

?>
