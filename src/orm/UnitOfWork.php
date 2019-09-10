<?php

namespace PPA\orm;

final class UnitOfWork implements \Symfony\Component\EventDispatcher\EventSubscriberInterface
{
    /**
     * An entity is in MANAGED state when its persistence is managed by an EntityManager.
     */
    const STATUS_MANAGED = 1;

    /**
     * An entity is new if it has just been instantiated (i.e. using the "new" operator)
     * and is not (yet) managed by an EntityManager.
     */
    const STATUS_NEW = 2;

    /**
     * A detached entity is an instance with persistent state and identity that is not
     * (or no longer) associated with an EntityManager (and a UnitOfWork).
     */
    const STATUS_DETACHED = 3;

    /**
     * A removed entity instance is an instance with a persistent identity,
     * associated with an EntityManager, whose persistent state will be deleted
     * on commit.
     */
    const STATUS_REMOVED = 4;
    
    /**
     *
     * @var array
     */
    private $_statuses = [self::STATUS_MANAGED, self::STATUS_NEW, self::STATUS_DETACHED, self::STATUS_REMOVED];
    
    /**
     *
     * @var \Closure
     */
    private $_invalidStateExceptionGenerator;

    /**
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    private $eventDispatcher;
    
    /**
     *
     * @var OriginsMap
     */
    private $originsMap;
    
    /**
     *
     * @var maps\IdentityMap
     */
    private $identityMap;

    /**
     *
     * @var maps\EntityStatesMap
     */
    private $entityStatesMap;

    public static function getSubscribedEvents(): array
    {
        return [
            event\entityManagement\EntityPersistEvent::NAME => "doRegisterEntity",
            event\entityManagement\EntityRemoveEvent::NAME  => "doRemoveEntity"
        ];
    }

    public function __construct(\Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher, EntityAnalyser $analyser, maps\OriginsMap $originsMap, maps\IdentityMap $identityMap)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->originsMap      = $originsMap;
        $this->identityMap     = $identityMap;
        $this->entityStatesMap = new maps\EntityStatesMap($analyser);
        
        $this->_invalidStateExceptionGenerator = function(int $status) {
            return \PPA\core\exceptions\ExceptionFactory::NotInDomain("Entity status '{$status}' is invalid. Allowed statuses are: '" . implode("', '", $this->_statuses) . "'");
        };
    }
    
    public function doRegisterEntity(event\entityManagement\EntityPersistEvent $event)
    {
        // dispatch another event
        // logging?
        
        $this->registerEntity($event->getEntity());
        
        // logging?
        // dispatch another event
    }
    
    public function doRemoveEntity(event\entityManagement\EntityRemoveEvent $event)
    {
        $this->removeEntity($event->getEntity());
    }
    
    private function registerEntity(entity\Serializable $entity)
    {
        $status = $this->entityStatesMap->getStatus($entity);
//        var_dump($status);
        
        switch ($status)
        {
            case self::STATUS_MANAGED:
                // nothing to do here; maybe logging?
                break;
            case self::STATUS_NEW:
                $this->originsMap->add($entity);
                $this->identityMap->add($entity);
                
                break;
            case self::STATUS_DETACHED:
                break;
            case self::STATUS_REMOVED:

                break;
            default:
                throw ($this->_invalidStateExceptionGenerator)($status);
        }
        
        
    }
    
    private function removeEntity(entity\Serializable $entity)
    {
        $status = $this->entityStatesMap->getStatus($entity);
        
        switch ($status)
        {
            case self::STATUS_MANAGED:

            case self::STATUS_NEW:

            case self::STATUS_DETACHED:
                break;
            case self::STATUS_REMOVED:

            default:
                throw ($this->_invalidStateExceptionGenerator)($status);
        }
        
//        $this->originsMap->addEntity($entity);
        
    }

}

?>
