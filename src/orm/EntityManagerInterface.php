<?php

namespace PPA\orm;

use PPA\dbal\query\builder\QueryBuilder;
use PPA\orm\entity\Serializable;
use PPA\orm\repository\RepositoryInterface;

/**
 *
 * @author siwe
 */
interface EntityManagerInterface
{
    public function close(): void;
    public function clear(): void;
    public function flush(): void;
    
    public function persist(Serializable $entity): void;
    public function merge(Serializable $entity): void;
    public function remove(Serializable $entity): void;
    
    public function retrieveRepository(string $classname): RepositoryInterface;
    
    public function retrieveQuerybuilder(): QueryBuilder;
}

?>
