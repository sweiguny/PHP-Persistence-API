<?php

namespace PPA\orm\repository;

use PPA\orm\Analysis;
use PPA\orm\EntityManagerInterface;
use PPA\orm\maps\EntityStatesMap;
use PPA\orm\maps\IdentityMap;
use PPA\orm\TransactionManager;

class EntityRepositoryFactory
{
    /**
     *
     * @var array
     */
    private $repoList = [];
    
    public function createRepository(string $repositoryClass, EntityManagerInterface $entityManager, TransactionManager $transactionManager, IdentityMap $identityMap, EntityStatesMap $entityStatesMap, Analysis $metaData): EntityRepository
    {
        if (isset($this->repoList[$repositoryClass]))
        {
            $repository = $this->repoList[$repositoryClass];
        }
        else
        {
            $repository = $this->repoList[$repositoryClass] = new $repositoryClass($entityManager, $transactionManager, $identityMap, $entityStatesMap, $metaData);
        }
        
        return $repository;
        
//        return isset($this->repoList[$repositoryClass])
//            ? $this->repoList[$repositoryClass]
//            : new $repositoryClass($entityManager, $transactionManager, $analyser);
    }
}

?>
