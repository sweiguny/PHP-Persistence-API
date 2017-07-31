<?php

namespace PPA\orm;

use PPA\orm\entity\Serializable;
use PPA\orm\events\EntityPersistEvent;
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
     * @var EntityAnalyzer 
     */
    private $analyzer;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->identityMap   = new IdentityMap();
        $this->analyzer      = new EntityAnalyzer();
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
        
        $this->identityMap->add($entity, "id");
    }
    
}

?>
