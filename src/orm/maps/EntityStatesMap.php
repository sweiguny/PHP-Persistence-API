<?php

namespace PPA\orm\maps;

use PPA\core\exceptions\ExceptionFactory;
use PPA\orm\entity\Serializable;
use PPA\orm\EntityAnalyser;
use SplObjectStorage;

class EntityStatesMap
{
    use MapTrait;
    
    /**
     *
     * @var SplObjectStorage
     */
    private $map;
    
    /**
     *
     * @var EntityAnalyser
     */
    private $analyser;
    
    public function __construct(EntityAnalyser $analyser)
    {
        $this->analyser = $analyser;
        
        $this->map = new SplObjectStorage(); // Be carful on interations
//        $this->map->at
    }
    
    public function add(Serializable $entity, int $status, string $eventName)
    {
        list($classname, $key) = $this->getImperatives($entity);
        
        if ($this->contains($entity))
        {
            throw ExceptionFactory::AlreadyExistentInEntityStatesMap($classname, $key);
        }
        else
        {
            
        }
        
        $statusObject = new EntityStatus($status, $eventName);
        
        $this->map->attach($entity);
    }
    
    public function remove(Serializable $entity)
    {
        list($classname, $key) = $this->getImperatives($entity);
        
    }
    
    public function contains(Serializable $entity)
    {
        list($classname, $key) = $this->getImperatives($entity);
        
    }
    
    public function getStatus(Serializable $entity): int
    {
//        var_dump(__METHOD__);
//        list($classname, $key) = $this->getImperatives($entity);
        
        if ($this->map->contains($entity))
        {
            echo "here";
        }
        else
        {
            return \PPA\orm\UnitOfWork::STATUS_NEW;
        }
    }
    
    public function size($param)
    {
        
    }
    
    public function clear($param)
    {
        
    }
    
}

class EntityStatus
{
    /**
     *
     * @var int
     */
    private $currentStatus;
    
    /**
     *
     * @var int
     */
    private $beforeStatus;
    
    /**
     *
     * @var string
     */
    private $eventName;
    
    public function __construct(int $currentStatus, string $eventName)
    {
        $this->currentStatus = $currentStatus;
        $this->eventName     = $eventName;
    }

    public function getCurrentStatus()
    {
        return $this->currentStatus;
    }

    public function setCurrentStatus(int $currentStatus)
    {
        $this->beforeStatus  = $this->currentStatus;
        $this->currentStatus = $currentStatus;
    }

}

?>
