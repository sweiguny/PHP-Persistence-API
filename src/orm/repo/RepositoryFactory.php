<?php

namespace PPA\orm\repo;

use PPA\orm\UnitOfWork;

class RepositoryFactory
{
    /**
     *
     * @var array
     */
    private $repositories = [];
    
    /**
     * 
     * @var UnitOfWork
     */
    private $unitOfWork;

    public function __construct(UnitOfWork $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }
    
    public function getRepository(string $classname)
    {
        if (!isset($this->repositories[$classname]))
        {
            $this->repositories[$classname] = $this->createRepository($classname);
        }
        
        return $this->repositories[$classname];
    }

    private function createRepository(string $classname)
    {
        return new DefaultRepository($this->unitOfWork, $classname);
    }
    
}

?>
