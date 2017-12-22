<?php

namespace PPA\orm;

use PPA\orm\entity\Serializable;
use PPA\orm\event\entityManagement\EntityPersistEvent;
use PPA\orm\event\entityManagement\EntityRemoveEvent;
use PPA\orm\event\entityManagement\FlushEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UnitOfWork implements EventSubscriberInterface
{
    /**
     *
     * @var EntityManager
     */
    private $entityManager;
    
    /**
     * 
     * @var IdentityMap
     */
    private $identityMap;
    
    /**
     * 
     * @var EntityAnalyser 
     */
    private $analyser;

    /**
     * 
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EntityPersistEvent::NAME => "addEntity",
            EntityRemoveEvent::NAME  => "removeEntity",
            FlushEvent::NAME         => "writeChanges"
        ];
    }
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->identityMap   = new IdentityMap();
        $this->analyser      = new EntityAnalyser();
    }
    
    public function getChangeSet(Serializable $entity)
    {
        
    }
    
    protected function writeChanges(FlushEvent $event)
    {
        $managedEntities = $this->identityMap->dumpMapByObjectId();
        
        foreach ($managedEntities as $oid => $entity)
        {
            $changeSet = $this->getChangeSet($entity);
            
            
        }
        
        die("here");
    }

    public function addEntity(EntityPersistEvent $event)
    {
        $entityManager = $event->getEntityManager();
        $entity        = $event->getEntity();
        
        $metaData = $this->analyser->getMetaData($entity);
        
        $key = $metaData->getPrimaryProperty()->getValue($entity);
        
        $this->identityMap->add($entity, $key);
    }

    public function removeEntity(EntityRemoveEvent $event)
    {
        $entityManager = $event->getEntityManager();
        $entity        = $event->getEntity();
        
        $metaData = $this->analyser->getMetaData($entity);
        
        $key = $metaData->getPrimaryProperty()->getValue($entity);
        
        $this->identityMap->remove($entity, $key);
    }

    public function getIdentityMap(): IdentityMap
    {
        return $this->identityMap;
    }
    
}

?>
