<?php

namespace PPA\orm\repo;

use PPA\orm\UnitOfWork;

class DefaultRepository
{
    
    /**
     * 
     * @var UnitOfWork
     */
    private $unitOfWork;
    
    /**
     *
     * @var string
     */
    private $classname;

    public function __construct(UnitOfWork $unitOfWork, string $classname)
    {
        $this->unitOfWork = $unitOfWork;
        $this->classname  = $classname;
    }
    
    public function findBy($key)
    {
        $entity = $this->unitOfWork->getIdentityMap()->retrieve($this->classname, $key);
        
        if ($entity == null)
        {
            // make query using querybuilder
            // add result to identymap
        }
        
        return $entity;
    }
    
}

?>
