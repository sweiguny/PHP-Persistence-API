<?php

namespace PPA\orm;

use PPA\dbal\TransactionManager;
use PPA\orm\repo\RepositoryFactory;

class EntityManager
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

    public function __construct(TransactionManager $transactionManager)
    {
        $this->transactionManager = $transactionManager;
        $this->unitOfWork         = new UnitOfWork($this);
        $this->repositoryFactory  = new RepositoryFactory($this->unitOfWork);
    }
    
    public function getRepository($classname)
    {
        return $this->repositoryFactory->getRepository($classname);
    }
    
    public function persist($entity)
    {
        // add entity to uow
    }
    
    public function remove($entity)
    {
        //remove entity to uow
    }
    
    public function getChangeSet($entity)
    {
        return $this->unitOfWork->getChangeSet($entity);
    }
    
    public function flush()
    {
        // process uof and 
    }

}

?>
