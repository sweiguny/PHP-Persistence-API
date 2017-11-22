<?php

namespace PPA\orm;

use PPA\orm\entity\Serializable;
use PPA\orm\event\EntityPersistEvent;
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
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->identityMap   = new IdentityMap();
        $this->analyser      = new EntityAnalyser();
    }

    public function getIdentityMap(): IdentityMap
    {
        return $this->identityMap;
    }
    
    public function getChangeSet(Serializable $entity)
    {
        
    }

    /**
     * 
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EntityPersistEvent::NAME => "addEntity"
        ];
    }

    public function addEntity(EntityPersistEvent $event)
    {
        $entityManager = $event->getEntityManager();
        $entity        = $event->getEntity();
        
        $metaData = $this->analyser->analyse($entity);
        
        $this->identityMap->add($entity, "id");
    }
    
}

?>
