<?php

namespace PPA\orm\repository;

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
    
    public function getRepository(string $classname): DefaultRepository
    {
        if (!isset($this->repositories[$classname]))
        {
            $this->repositories[$classname] = $this->createRepository($classname);
        }
        
        return $this->repositories[$classname];
    }

    private function createRepository(string $classname): DefaultRepository
    {
        return new DefaultRepository($this->unitOfWork, $classname);
    }
    
}

?>
