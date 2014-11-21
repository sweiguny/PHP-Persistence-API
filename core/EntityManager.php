<?php

namespace PPA\core;

use DomainException;
use LogicException;
use PDO;
use PPA\core\exception\RelationException;
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
        $values          = [];
        
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
                
                // Only one-to-one-relations are processed here, because they are
                // mapped by a column in the origin entity table.
                if ($property->getRelation() instanceof OneToOne && !($value instanceof MockEntity)) {
                    
                    // Check cascade type, if persisting of related object is necessary.
                    if ($property->getRelation()->isCascadeTypePersist()) {
                        if ($value instanceof Entity) {
                            $this->persist($value);

                            $values[] = $this->emdm->getPrimaryProperty($property->getRelation()->getMappedBy())->getValue($value);
                            $query   .= " `{$property->getColumn()}` = ?,";
                        } else {
                            throw new DomainException("The value of {$classname}({$property->getName()}) is expected to be an instance of Entity (due to a one-to-one-Relation), but is " . gettype($value));
                        }
                    
                    // For the related object is not to persist, check its primary value,
                    // because the referencing property needs a value and cannot be null.
                    } else if ($this->emdm->getPrimaryProperty($property->getRelation()->getMappedBy())->getValue($value) == null) {
                        throw new RelationException("Cannot persist object of '{$property->getRelation()->getMappedBy()}', because primary property ('{$this->emdm->getPrimaryProperty($property->getRelation()->getMappedBy())->getName()}') of related object is null. Please check cascade type of property '{$property->getName()}' of class '{$classname}'");
                    }
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
//        \PPA\prettyDump($query);
//        \PPA\prettyDump($values);
        
        // Set primary after instertion.
        if ($isInsertion) {
            $primaryProperty->setValue($entity, $result);
        }
        
        
        // Process relations except one-to-one.
        foreach ($relations as $relation) {
            if ($relation instanceof OneToMany) {
                $values = $relation->getProperty()->getValue($entity);
                
                // Omit processing mock, because a mock indicates no changes.
                // If cascade type is unfittingly, prossecing is omitted, too.
                if (!($values instanceof MockEntityList) && $relation->isCascadeTypePersist()) {
                    $difference                = [];
                    $relationTableName         = $this->emdm->getTableName($relation->getMappedBy());
                    $primaryPropertyOfRelation = $this->emdm->getPrimaryProperty($relation->getMappedBy());

                    foreach ($values as $value) {
                        $propertiesOfRelation = $this->emdm->getPropertiesByColumn($relation->getMappedBy());

                        if ($propertiesOfRelation[$relation->getX_column()]->getValue($value) === null) {
                            $propertiesOfRelation[$relation->getX_column()]->setValue($value, $primaryProperty->getValue($entity));
                        }

                        $this->persist($value);
                        $difference[] = $primaryPropertyOfRelation->getValue($value);
                    }

                    // remove all entries from table, that were not to persist
                    if (!empty($difference)) {
                        $query2 = new PreparedQuery("DELETE FROM `{$relationTableName}` WHERE `{$relation->getX_column()}` = ? AND `{$primaryPropertyOfRelation->getName()}` NOT IN(" . implode(',', array_fill(0, count($difference), '?')) . ")");
                        $query2->getSingleResult(array_merge([$primaryProperty->getValue($entity)], $difference));
                    }
                }
            } else if ($relation instanceof ManyToMany) {
                $values = $relation->getProperty()->getValue($entity);
                
                // Omit processing mock, because a mock indicates no changes.
                // If cascade type is unfittingly, prossecing is omitted, too.
                if (!($values instanceof MockEntityList) && $relation->isCascadeTypePersist()) {
                    $primaries = array();

                    foreach ($values as $value) {
                        $this->persist($value);
                        $primaries[] = $this->emdm->getPrimaryProperty($relation->getMappedBy())->getValue($value);
                    }

                    $query = "DELETE FROM `{$relation->getJoinTable()}` WHERE `{$relation->getColumn()}` = ?";
                    $q     = new PreparedQuery($query);
                    $q->getSingleResult([$primaryProperty->getValue($entity)]);


                    $query = "INSERT INTO `{$relation->getJoinTable()}` SET `{$relation->getColumn()}` = ?, `{$relation->getX_column()}` = ?";
                    $q     = new PreparedQuery($query);

                    foreach ($primaries as $primary) {
                        $q->getSingleResult([$primaryProperty->getValue($entity), $primary]);
                    }
                }
            }
        }
        
        if ($isInsertion) {
            return $result;
        }
    }
    
    /**
     * Removes an entity from the database.
     * 
     * @param Entity $entity The entity to remove from database.
     * @return int The number of affected rows.
     */
    public function remove(Entity $entity) {
        $classname              = get_class($entity);
        $tablename              = $this->emdm->getTableName($classname);
        $primaryProperty        = $this->emdm->getPrimaryProperty($classname);
        $relations              = $this->emdm->getRelations($classname);
        $oneToOneRelatedObjects = [];
        
        if ($primaryProperty->getValue($entity) == null) {
            throw new LogicException("The primary property '{$primaryProperty->getName()}' of '{$classname}' is null, hence the entity cannot be deleted from database.");
        }
        
        foreach ($relations as $relation) {
            if ($relation instanceof OneToOne && $relation->isCascadeTypeRemove()) {
                
                // Get related objects before deleting from relation table,
                // because in case of a Mock, the lazy fetching wouldn't work.
                $relatedObject = $relation->getProperty()->getValue($entity);
                if ($relatedObject instanceof MockEntity) {
                    $relatedObject->exchange();
                    $relatedObject = $relation->getProperty()->getValue($entity);
                }
                
                // Check for not having a unpersisted related object.
                // Otherwise queries may have invalid syntax.
                if ($this->emdm->getPrimaryProperty($relation->getMappedBy())->getValue($relatedObject) != null) {
                    $oneToOneRelatedObjects[] = $relatedObject;
                }
            } else if ($relation instanceof OneToMany && $relation->isCascadeTypeRemove()) {
                foreach ($relation->getProperty()->getValue($entity) as $relo) {
                    $this->remove($relo);
                }
            } else if ($relation instanceof ManyToMany) {
                
                // Get related objects before deleting from relation table,
                // because in case of a Mock, the lazy fetching wouldn't work.
                $relatedObjects = $relation->getProperty()->getValue($entity);
                if ($relatedObjects instanceof MockEntityList) {
                    $relatedObjects->exchange();
                }
                
                $deleteFromJointable = new PreparedQuery("DELETE FROM `{$relation->getJoinTable()}` WHERE `{$relation->getColumn()}` = ?");
                $deleteFromJointable->getSingleResult([$primaryProperty->getValue($entity)]);
                
                if ($relation->isCascadeTypeRemove()) {
                    foreach ($relatedObjects as $relo) {
                        $this->remove($relo);
                    }
                }
            }
        }
        
        $deleteEntity = new PreparedQuery("DELETE FROM `{$tablename}` WHERE `{$primaryProperty->getColumn()}` = ?");
        $return = $deleteEntity->getSingleResult([$primaryProperty->getValue($entity)]);
        
        foreach ($oneToOneRelatedObjects as $relatedObject) {
            $this->remove($relatedObject);
        }
        
        return $return;
    }
    
    public function find($fullyQualifiedClassname, $primaryValue) {
        
    }
    
    public function begin() {
        if ($this->inTransaction()) {
            throw new TransactionException("Already in a transaction.");
        }
        $this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT, false);
        $this->conn->beginTransaction();
    }
    
    public function commit() {
        if (!$this->inTransaction()) {
            throw new TransactionException("Not in a transaction.");
        }
        $this->conn->commit();
        $this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    }
    
    public function rollback() {
        if (!$this->inTransaction()) {
            throw new TransactionException("Not in a transaction.");
        }
        $this->conn->rollBack();
        $this->conn->setAttribute(PDO::ATTR_AUTOCOMMIT, true);
    }
    
    public function inTransaction() {
        return $this->conn->inTransaction();
    }
}

?>