<?php

namespace PPA\orm\event\entityManagement;

use PPA\core\PPA;
use PPA\orm\entity\Serializable;
use PPA\orm\EntityManager;
use Symfony\Component\EventDispatcher\Event;

class EntityManagementEvent extends Event
{
    const NAME = PPA::EntityManagementEventPrefix . "persist";
    
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
