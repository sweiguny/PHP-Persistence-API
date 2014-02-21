<?php

namespace PPA\core;

use PDO;
use PPA\core\exception\TransactionException;
use PPA\core\mock\MockEntity;
use PPA\core\mock\MockEntityList;
use PPA\core\query\PreparedQuery;
use PPA\core\relation\ManyToMany;
use PPA\core\relation\OneToMany;
use PPA\core\relation\OneToOne;
use PPA\PPA;

class EntityManager {

    private static $instance;

    /**
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
    private $conn;
    
    /**
     * @var EntityMetaDataMap
     */
    private $emdm;
    
    private function __clone() { }
    private function __construct() {
        $this->conn = PPA::getInstance()->getConnection();
        $this->emdm = EntityMetaDataMap::getInstance();
    }
    
    /**
     * This method persists an entity. This means, that all changes of the
     * specified entity are written to the database.
     * 
     * Already mapped entities are updated. Others are inserted. Wheter an entity
     * is mapped is indicated by a set primary value.
     * 
     * All relations are processed as well, unless they're not presented by a
     * mock. Because a mock indicates, that the relation hasn't been touched nor
     * changed - so there is no persistence activity required.
     * 
     * @param Entity $entity The entity to persist
     */
    public function persist(Entity $entity) {
        $classname       = get_class($entity);
        $tablename       = $this->emdm->getTableName($classname);
        $primaryProperty = $this->emdm->getPrimaryProperty($classname);
        $properties      = $this->emdm->getPropertiesByColumn($classname);
        $relations       = $this->emdm->getRelations($classname);
        $isInsertion     = $primaryProperty->getValue($entity) === null;
        $values          = array();
        
        // Create query string.
        if ($isInsertion) {
            $query = "INSERT INTO `{$tablename}` SET";
        } else {
            $query = "UPDATE `{$tablename}` SET";
        }
        
        // Iterare over properties, to map each to its dedicated column.
        foreach ($properties as $property) {
            if ($property->hasRelation()) {
                $value = $property->getValue($entity);
                
                // Only one-to-one-relations are processed, because they are
                // mapped by a column in the origin entity table.
                if ($property->getRelation() instanceof OneToOne && !($value instanceof MockEntity)) {
                    $this->persist($value);
                    
                    $foreign  = $this->emdm->getPrimaryProperty($property->getRelation()->getMappedBy())->getValue($value);
                    $values[] = $foreign;
                    $query   .= " `{$property->getColumn()}` = ?,";
                }
            } else if ($property->isPrimary() && $isInsertion) {
                // Primaries should be set automatically on insertions.
                // This can be omitted, but it brings more clarity.
                $query .= " `{$property->getColumn()}` = NULL,";
            } else {
                $query   .= " `{$property->getColumn()}` = ?,";
                $values[] = $property->getValue($entity);
            }
        }
        $query = substr($query, 0, -1); // Remove last comma.
        
        
        // Restrict update to specific row.
        if (!$isInsertion) {
            $query   .= " WHERE `{$primaryProperty->getColumn()}` = ?";
            $values[] = $primaryProperty->getValue($entity);
        }
        
        
        $q      = new PreparedQuery($query);
        $result = $q->getSingleResult($values); // Execute query
        
        // Set primary after instertion.
        if ($isInsertion) {
            $primaryProperty->setValue($entity, $result);
        }
        
        
        // Process relations exception one-to-one.
        foreach ($relations as $relation) {
            if ($relation instanceof OneToMany) {
                $values = $relation->getProperty()->getValue($entity);
                
                // Omit processing mock, because a mock indicates no changes.
                if (!($values instanceof MockEntityList)) {
                    foreach ($values as $value) {
                        $propertiesOfRelation = $this->emdm->getPropertiesByColumn($relation->getMappedBy());
                        
                        if ($propertiesOfRelation[$relation->getX_column()]->getValue($value) === null) {
                            $propertiesOfRelation[$relation->getX_column()]->setValue($value, $primaryProperty->getValue($entity));
                        }
                        
                        $this->persist($value);
                    }
                }
            } else if ($relation instanceof ManyToMany) {
                $values = $relation->getProperty()->getValue($entity);
                
                // Omit processing mock, because a mock indicates no changes.
                if (!($values instanceof MockEntityList)) {
                    $primaries = array();
                    
                    foreach ($values as $value) {
                        $this->persist($value);
                        $primaries[] = $this->emdm->getPrimaryProperty($relation->getMappedBy())->getValue($value);
                    }
                    
                    # TODO: log query and prepared statements
                    $query = "DELETE FROM `{$relation->getJoinTable()}` WHERE `{$relation->getColumn()}` = ?";
                    $q     = new PreparedQuery($query);
                    $q->getSingleResult(array($primaryProperty->getValue($entity)));
                    
                    
                    $query = "INSERT INTO `{$relation->getJoinTable()}` SET `{$relation->getColumn()}` = ?, `{$relation->getX_column()}` = ?";
                    $q     = new PreparedQuery($query);
                    
                    foreach ($primaries as $primary) {
                        # TODO: log query
                        $q->getSingleResult(array($primaryProperty->getValue($entity), $primary));
                    }
                }
            }
        }
    }
    
    /**
     * Removes an entity from the database.
     * 
     * @param Entity $entity The entity to remove from database.
     * @return int The number of affected rows.
     */
    public function remove(Entity $entity) {
        $classname       = get_class($entity);
        $tablename       = $this->emdm->getTableName($classname);
        $primaryProperty = $this->emdm->getPrimaryProperty($classname);
        
        $query = "DELETE FROM `{$tablename}` WHERE `{$primaryProperty->getColumn()}` = ?";
        $q     = new PreparedQuery($query);
        # TODO: make sort of cascading options.
        return $q->getSingleResult(array($primaryProperty->getValue($entity)));
    }
    
    # TODO: outsource the following methods.
    
    public function begin() {
        if ($this->inTransaction()) {
            throw new TransactionException("Already in an transaction.");
        }
        $this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        $this->conn->beginTransaction();
    }
    
    public function commit() {
        if (!$this->inTransaction()) {
            throw new TransactionException("Not in an transaction.");
        }
        $this->conn->commit();
        $this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    }
    
    public function rollback() {
        if (!$this->inTransaction()) {
            throw new TransactionException("Not in an transaction.");
        }
        $this->conn->rollBack();
        $this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    }
    
    public function inTransaction() {
        return $this->conn->inTransaction();
    }
}

?>