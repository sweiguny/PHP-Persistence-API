<?php

namespace PPA\orm;

use Closure;
use PPA\core\exceptions\ExceptionFactory;
use PPA\orm\entity\Serializable;
use PPA\orm\event\entityManagement\EntityPersistEvent;
use PPA\orm\event\entityManagement\EntityRemoveEvent;
use PPA\orm\maps\EntityStatesMap;
use PPA\orm\maps\IdentityMap;
use PPA\orm\maps\OriginsMap;
use PPA\orm\repository\EntityRepository;
use PPA\orm\repository\EntityRepositoryFactory;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class UnitOfWork implements EventSubscriberInterface
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
     * @var Closure
     */
    private $_invalidStateExceptionGenerator;

    /**
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    
    /**
     *
     * @var OriginsMap
     */
    private $originsMap;
    
    /**
     *
     * @var IdentityMap
     */
    private $identityMap;

    /**
     *
     * @var EntityStatesMap
     */
    private $entityStatesMap;
    
    /**
     *
     * @var EntityRepositoryFactory
     */
    private $repositoryFactory;
    
    public static function getSubscribedEvents(): array
    {
        return [
            EntityPersistEvent::NAME => "doRegisterEntity",
            EntityRemoveEvent::NAME  => "doRemoveEntity"
        ];
    }

    public function __construct(EventDispatcherInterface $eventDispatcher, EntityAnalyser $analyser, OriginsMap $originsMap, IdentityMap $identityMap)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->originsMap      = $originsMap;
        $this->identityMap     = $identityMap;
        
        $this->entityStatesMap   = new EntityStatesMap($analyser);
        $this->repositoryFactory = new EntityRepositoryFactory();
        
        $this->_invalidStateExceptionGenerator = function(int $status) {
            return ExceptionFactory::NotInDomain("Entity status '{$status}' is invalid. Allowed statuses are: '" . implode("', '", $this->_statuses) . "'");
        };
    }
    
    public function doRegisterEntity(EntityPersistEvent $event)
    {
        // dispatch another event
        // logging?
        
        $this->registerEntity($event->getEntity());
        
        // logging?
        // dispatch another event
    }
    
    public function doRemoveEntity(EntityRemoveEvent $event)
    {
        $this->removeEntity($event->getEntity());
    }
    
    private function registerEntity(Serializable $entity)
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
    
    private function removeEntity(Serializable $entity)
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
    
    public function getRepository(EntityManagerInterface $entityManager, TransactionManager $transactionManager, Analysis $metaData): EntityRepository
    {
        return $this->repositoryFactory->createRepository($metaData->getRepositoryClass(), $entityManager, $transactionManager, $this->identityMap, $this->entityStatesMap, $metaData);
    }

}

?>
