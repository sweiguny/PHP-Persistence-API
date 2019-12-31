<?php

namespace PPA\orm\repository;

use PPA\orm\Analysis;
use PPA\orm\entity\Serializable;
use PPA\orm\EntityManager;
use PPA\orm\maps\EntityStatesMap;
use PPA\orm\maps\IdentityMap;
use PPA\orm\TransactionManager;
use function PPA\dbal\query\builder\AST\catalogObjects\Field;
use function PPA\dbal\query\builder\AST\catalogObjects\Table;
use function PPA\dbal\query\builder\AST\expressions\Parameter;
use function PPA\dbal\query\builder\AST\operators\Equals;

class EntityRepository
{
    const PRIMARY_DELIMITER = "||";
    
    /**
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     *
     * @var TransactionManager
     */
    private $transactionManager;
    
    /**
     *
     * @var IdentityMap
     */
    private $identityMap;

    /**
     *
     * @var EntityStatesMap
     */
    private $entityStatesMap;
    
    /**
     *
     * @var Analysis
     */
    private $metaData;
    
    /**
     * 
     * @var array key=primary, value=objectHash
     */
    private $primaryKeyHashMap = [];

    public function __construct(EntityManager $entityManager, TransactionManager $transactionManager, IdentityMap $identityMap, EntityStatesMap $entityStatesMap, Analysis $metaData)
    {
        $this->entityManager      = $entityManager;
        $this->transactionManager = $transactionManager;
        $this->identityMap        = $identityMap;
        $this->entityStatesMap    = $entityStatesMap;
        $this->metaData           = $metaData;
    }
    
    public function findByPrimary(array $primary): ?Serializable
    {
        $table = $this->metaData->getTableName();
        $field = $this->metaData->getPrimaryProperty()->getColumn()->getName();
        $pKey  = implode(self::PRIMARY_DELIMITER, $primary);
        
        // TODO: make a fork for cache retrivals
        
        /*
         * TODO:
         * - Check if there's a (object) hash for the class-primary-combi
         * - if yes, fetch from entity map
         * - if not, fetch from DB
         */
        
        if (isset($this->primaryKeyHashMap[$pKey]))
        {
            $result = $this->identityMap->getEntity($this->primaryKeyHashMap[$pKey]);
        }
        else
        {
            $qb = $this->entityManager->retrieveQuerybuilder();
            $qb->select()->from(Table($table))->where()->criteria(Equals(Field($field), Parameter()));
            
            $statement = $this->entityManager->createPreparedStatement($qb->sql());
            $result    = $statement->execute([$primary])->getSingleResult($this->metaData->getClassname());
            var_dump($result);
            if ($result != null)
            {
                $this->primaryKeyHashMap[$pKey] = spl_object_hash($result);
            }
        }
        
//        $this->entityStatesMap->contains($entity);
        
        return $result;
    }
    
}

?>
