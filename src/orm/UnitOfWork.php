<?php

namespace PPA\orm;

use ReflectionClass;

class UnitOfWork
{
    /**
     *
     * @var EntityManager
     */
    private $entityManager;
    
    /**
     * 
     * @var IdentityMap
     */
    private $identityMap;
    
    /**
     * 
     * @var EntityAnalyzer 
     */
    private $analyzer;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->identityMap   = new IdentityMap();
        $this->analyzer      = new EntityAnalyzer();
    }

    public function getIdentityMap(): IdentityMap
    {
        return $this->identityMap;
    }
    
    public function getChangeSet($entity)
    {
        
    }

}

?>
