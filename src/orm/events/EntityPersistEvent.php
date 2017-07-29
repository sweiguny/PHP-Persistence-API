<?php

namespace PPA\orm\events;

use PPA\core\PPA;
use PPA\orm\entity\Serializable;
use PPA\orm\EntityManager;
use Symfony\Component\EventDispatcher\Event;

class EntityPersistEvent extends Event
{
    const NAME = PPA::EventPrefix . "persist";
    
    /**
     *
     * @var EntityManager
     */
    private $entityManager;
    
    /**
     *
     * @var Serializable
     */
    private $entity;
    
    public function __construct(EntityManager $entityManager, Serializable $entity)
    {
        $this->entityManager = $entityManager;
        $this->entity        = $entity;
    }

    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    public function getEntity(): Serializable
    {
        return $this->entity;
    }
    
}

?>
