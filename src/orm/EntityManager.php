<?php

namespace PPA\orm;

use PPA\core\EventDispatcher;
use PPA\dbal\TransactionManager;
use PPA\orm\entity\ChangeSet;
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
    
    public function persist(Serializable $entity): void
    {
        $event = new EntityPersistEvent($this, $entity);
        
        $this->eventDispatcher->dispatch(EntityPersistEvent::NAME, $event);
    }
    
    public function remove(Serializable $entity): void
    {
        $event = new EntityRemoveEvent($this, $entity);
        
        $this->eventDispatcher->dispatch(EntityRemoveEvent::NAME, $event);
    }
    
    public function getChangeSet(Serializable $entity): ChangeSet
    {
        return $this->unitOfWork->getChangeSet($entity);
    }
    
    public function flush(): void
    {
        $this->eventDispatcher->dispatch(FlushEvent::NAME, new FlushEvent());
    }
    
    private function doFlush(TransactionCommitEvent $event): void
    {
        $this->flush();
    }
    
    public function getTransactionManager(): TransactionManager
    {
        return $this->transactionManager;
    }

}

?>
