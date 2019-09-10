<?php

namespace PPA\orm;

use PPA\dbal\query\builder\QueryBuilder;
use PPA\orm\entity\Serializable;
use PPA\orm\event\entityManagement\EntityPersistEvent;
use PPA\orm\event\entityManagement\EntityRemoveEvent;
use PPA\orm\repository\EntityRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EntityManager implements EntityManagerInterface
{
    /**
     *
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     *
     * @var UnitOfWork
     */
    private $unitOfWork;
    
    /**
     *
     * @var EntityAnalyser
     */
    private $analyser;

    public function __construct(EventDispatcherInterface $eventDispatcher, UnitOfWork $unitOfWork, EntityAnalyser $analyser)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->unitOfWork      = $unitOfWork;
        $this->analyser        = $analyser;
        
        $this->eventDispatcher->addSubscriber($this->unitOfWork);
        
        // Maybe initialize in that way?
//        $this->eventDispatcher->addSubscriber(new UnitOfWork($eventDispatcher, new OriginsMap()));
    }
    
    public function clear(): void
    {
        
    }

    public function close(): void
    {
        
    }

    public function flush(): void
    {
        
    }

    public function merge(Serializable $entity): void
    {
        
    }

    public function persist(Serializable $entity): void
    {
        $this->eventDispatcher->dispatch(EntityPersistEvent::NAME, new EntityPersistEvent($this, $entity));
        
        
    }

    public function remove(Serializable $entity): void
    {
        $this->eventDispatcher->dispatch(EntityRemoveEvent::NAME, new EntityRemoveEvent($this, $entity));
    }

    public function retrieveQuerybuilder(): QueryBuilder
    {
        
    }

    public function retrieveRepository(string $classname): EntityRepository
    {
        $repositoryClass = $this->analyser->getMetaData($classname)->getRepositoryClass();
        
        return new $repositoryClass();
    }
    
    public function findByPrimary(string $classname, array $primary)
    {
        $repo = $this->retrieveRepository($classname);
        print_r($repo);
    }

}

?>
