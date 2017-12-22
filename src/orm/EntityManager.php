<?php

namespace PPA\orm;

use PPA\core\EventDispatcher;
use PPA\dbal\TransactionManager;
use PPA\orm\entity\Serializable;
use PPA\orm\event\entityManagement\EntityPersistEvent;
use PPA\orm\event\entityManagement\EntityRemoveEvent;
use PPA\orm\event\entityManagement\FlushEvent;
use PPA\orm\event\transactions\TransactionCommitEvent;
use PPA\orm\repository\DefaultRepository;
use PPA\orm\repository\RepositoryFactory;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EntityManager implements EventSubscriberInterface
{
    /**
     *
     * @var TransactionManager 
     */
    private $transactionManager;

    /**
     * 
     * @var UnitOfWork
     */
    private $unitOfWork;
    
    /**
     *
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     *
     * @var EventDispatcher
     */
    private $eventDispatcher;

    public static function getSubscribedEvents(): array
    {
        return [
            TransactionCommitEvent::NAME => "doFlush"
        ];
    }

    public function __construct(TransactionManager $transactionManager, EventDispatcher $eventDispatcher)
    {
        $this->transactionManager = $transactionManager;
        $this->eventDispatcher    = $eventDispatcher;
        $this->unitOfWork         = new UnitOfWork($this);
        $this->repositoryFactory  = new RepositoryFactory($this->unitOfWork);
        
        $this->eventDispatcher->addSubscriber($this->unitOfWork);
    }
    
    public function getRepository($classname): DefaultRepository
    {
        return $this->repositoryFactory->getRepository($classname);
    }
    
    public function persist(Serializable $entity)
    {
        $event = new EntityPersistEvent($this, $entity);
        
        $this->eventDispatcher->dispatch(EntityPersistEvent::NAME, $event);
    }
    
    public function remove(Serializable $entity)
    {
        $event = new EntityRemoveEvent($this, $entity);
        
        $this->eventDispatcher->dispatch(EntityRemoveEvent::NAME, $event);
    }
    
    public function getChangeSet(Serializable $entity)
    {
        return $this->unitOfWork->getChangeSet($entity);
    }
    
    public function flush()
    {
        $this->eventDispatcher->dispatch(FlushEvent::NAME, new FlushEvent());
    }
    
    private function doFlush(TransactionCommitEvent $event)
    {
        $this->flush();
    }

}

?>
