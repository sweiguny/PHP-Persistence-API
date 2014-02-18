<?php

namespace PPA\core;

use PDO;
use PPA\Bootstrap;
use PPA\core\mock\MockEntity;
use PPA\core\query\Query;
use PPA\core\relation\ManyToMany;
use PPA\core\relation\OneToMany;
use PPA\core\relation\OneToOne;

class EntityManager {

    private static $instance;

    /**
     * 
     * @return EntityManager
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * @var PDO the connection
     */
    private $pdo;
    
    /**
     * @var EntityMetaDataMap
     */
    private $emdm;

    private function __construct() {
        $this->pdo  = Bootstrap::getPDO();
        $this->emdm = EntityMetaDataMap::getInstance();
    }
    


    public function persist(Entity $entity) {
        $classname       = get_class($entity);
        $tablename       = $this->emdm->getTableName($classname);
        $primaryProperty = $this->emdm->getPrimaryProperty($classname);
        $properties      = $this->emdm->getPropertiesByColumn($classname);
        $relations       = $this->emdm->getRelations($classname);
        $isInsertion     = $primaryProperty->getValue($entity) === null;
        
        
        if ($isInsertion) {
            $query = "INSERT INTO `{$tablename}` SET";
        } else {
            $query = "UPDATE `{$tablename}` SET";
        }
        
        
        foreach ($properties as $property) {
            if ($property->hasRelation()) {
                $value = $property->getValue($entity);
                
                if ($property->getRelation() instanceof OneToOne && !($value instanceof MockEntity)) {
                    $this->persist($value);
                    
                    $foreign = $this->emdm->getPrimaryProperty($property->getRelation()->getMappedBy())->getValue($value);
                    $query  .= " `{$property->getColumn()}` = '{$foreign}',";
                }
            } else if ($property->isPrimary() && $isInsertion) {
                $query .= " `{$property->getColumn()}` = NULL,";
            } else {
                $query .= " `{$property->getColumn()}` = '{$property->getValue($entity)}',";
            }
        }
        
        $query = substr($query, 0, -1);
        if (!$isInsertion) {
            $query .= " WHERE `{$primaryProperty->getColumn()}` = {$primaryProperty->getValue($entity)}";
        }
        \PPA\prettyDump($query); # TODO: Log queries
        $q = new Query($query);
        
        $result = $q->getSingleResult();
        if ($isInsertion) {
            $primaryProperty->setValue($entity, $result);
        }
        \PPA\prettyDump($primaryProperty->getValue($entity));
        
        
        foreach ($relations as $relation) {
//            \PPA\prettyDump($relation);
            if ($relation instanceof OneToMany) {
                
                
                
            } else if ($relation instanceof ManyToMany) {
                
                $values = $relation->getProperty()->getValue($entity);
                if (!($values instanceof mock\MockEntityList)) {
                    
                    \PPA\prettyDump($values);
                    
                    $primaries = array();
                    
                    foreach ($values as $value) {
                        $this->persist($value);
                        
                        $primaryProperty = $this->emdm->getPrimaryProperty($relation->getMappedBy());
                        $primaryValue    = $primaryProperty->getValue($value);
                        $primaries[]     = $primaryValue;
                    }
                    
                    \PPA\prettyDump($values);
                    \PPA\prettyDump($primaries);
                    
                    // delete from jointable where column = primaryvalue and x_column not in $primaries
                    
                }
            }
        }
        
//        \PPA\prettyDump($result);
//        return $result;
    }
    
    public function remove(Entity $entity) {
        
    }
    
}

?>