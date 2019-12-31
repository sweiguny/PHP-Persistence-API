<?php

namespace PPA\orm;

use PPA\dbal\drivers\concrete\MySQLDriver;
use PPA\dbal\query\builder\QueryBuilder;
use PPA\dbal\query\PreparedStatement;
use PPA\dbal\query\Statement;
use PPA\dbal\query\StatementInterface;
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
     * @var EntityAnalyser
     */
    private $analyser;
    
    /**
     *
     * @var QueryBuilder
     */
    private $queryBuilder;

    public function __construct(EventDispatcherInterface $eventDispatcher, TransactionManager $transactionManager, UnitOfWork $unitOfWork, EntityAnalyser $analyser)
    {
        $this->eventDispatcher    = $eventDispatcher;
        $this->transactionManager = $transactionManager;
        $this->unitOfWork         = $unitOfWork;
        $this->analyser           = $analyser;
        
        $this->queryBuilder = new QueryBuilder(new MySQLDriver());
        
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
        $this->queryBuilder->clear(); // Macht das überhaupt sinn?!?!
        return $this->queryBuilder;
    }

    public function getRepository(string $classname): EntityRepository
    {
        return $this->unitOfWork->getRepository($this, $this->transactionManager, $this->analyser->getMetaData($classname));
        
        // TODO: Get that from DI container
//        return new $repositoryClass($this, $this->transactionManager, $this->analyser);
    }
    
    public function findByPrimary(string $classname, array $primary): ?Serializable
    {
        $repo = $this->getRepository($classname);
        return $repo->findByPrimary($primary);
    }

    public function createStatement(string $statement): StatementInterface
    {
        return new Statement($this->transactionManager->getConnection(), $statement);
    }
    
    public function createPreparedStatement(string $statement): StatementInterface
    {
        return new PreparedStatement($this->transactionManager->getConnection(), $statement);
    }
}

?>
